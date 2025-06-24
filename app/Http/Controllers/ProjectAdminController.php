<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectExtension;
use App\Models\ProjectTimelineEvent;
use App\Models\TalentAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectAdminController extends Controller
{
    /**
     * Display pending projects for admin approval
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $query = Project::with([
            'recruiter.user',
            'assignments.talent.user',
            'extensions',
            'pendingExtensions'
        ])->latest();        // Apply status filter
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->where('status', 'pending_admin');
                    break;
                case 'active':
                    $query->where('status', 'active');
                    break;
                case 'extensions':
                    $query->whereHas('extensions', function($q) {
                        $q->where('status', 'pending');
                    });
                    break;
                case 'closure_requested':
                    $query->where('status', 'closure_requested');
                    break;
                default:
                    break;
            }
        }        $projects = $query->paginate(10);

        $pendingCount = Project::where('status', 'pending_admin')->count();
        $activeCount = Project::where('status', 'active')->count();
        $closureRequestCount = Project::where('status', 'closure_requested')->count();
        $title = 'Project Management';

        return view('admin.projects.index', compact('projects', 'pendingCount', 'activeCount', 'closureRequestCount', 'title'));
    }    /**
     * Show project details for admin review
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $project->load([
            'recruiter.user',
            'assignments.talent.user',
            'extensions' => function($query) {
                $query->with('requester')->orderBy('created_at', 'desc');
            },
            'timelineEvents' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'adminApprovedBy'
        ]);        $title = 'Project Details';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.projects.show', compact('project', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Approve or reject project
     */
    public function approve(Request $request, Project $project)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);        if ($project->status !== 'pending_admin') {
            return redirect()->back()->with('error', 'Project has already been reviewed.');
        }        $project->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'admin_approved_at' => $request->status === 'approved' ? now() : null,
            'admin_approved_by' => Auth::id()
        ]);

        // Create timeline event
        ProjectTimelineEvent::create([
            'project_id' => $project->id,
            'event_type' => $request->status === 'approved' ? 'approved' : 'rejected',
            'description' => "Project {$request->status} by admin" . ($request->admin_notes ? ": {$request->admin_notes}" : ''),
            'user_id' => Auth::id()
        ]);

        $message = $request->status === 'approved'
            ? 'Project approved successfully!'
            : 'Project rejected successfully.';

        return redirect()->route('admin.projects.index')->with('success', $message);
    }

    /**
     * Reject a project
     */
    public function reject(Request $request, Project $project)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        if ($project->status !== 'pending_admin') {
            return redirect()->back()->with('error', 'This project cannot be rejected.');
        }

        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $project->update([
                'status' => 'cancelled',
                'admin_notes' => $validated['admin_notes'],
                'admin_approved_by' => Auth::id()
            ]);

            // Create timeline event
            ProjectTimelineEvent::create([
                'project_id' => $project->id,
                'event_type' => 'rejected',
                'description' => "Project '{$project->title}' rejected by admin {$user->name}: {$validated['admin_notes']}",
                'user_id' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('admin.projects.index')->with('success', 'Project rejected successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to reject project.');
        }
    }

    /**
     * Approve a project extension
     */
    public function approveExtension(Request $request, ProjectExtension $extension)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        if (!$extension->isPending()) {
            return redirect()->back()->with('error', 'This extension cannot be approved.');
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $extension->update([
                'status' => ProjectExtension::STATUS_APPROVED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'] ?? null
            ]);

            // Update project end date
            $extension->project->update([
                'expected_end_date' => $extension->new_end_date
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $extension->project_id,
                ProjectTimelineEvent::EVENT_EXTENSION_APPROVED,
                "Extension approved by admin {$user->name} - new end date: {$extension->new_end_date->format('Y-m-d')}",
                $admin,
                [
                    'admin_name' => $user->name,
                    'old_end_date' => $extension->old_end_date->format('Y-m-d'),
                    'new_end_date' => $extension->new_end_date->format('Y-m-d'),
                    'extension_days' => $extension->extension_days,
                    'review_notes' => $validated['review_notes'] ?? null
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Extension approved successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to approve extension.');
        }
    }

    /**
     * Reject a project extension
     */
    public function rejectExtension(Request $request, ProjectExtension $extension)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        if (!$extension->isPending()) {
            return redirect()->back()->with('error', 'This extension cannot be rejected.');
        }

        $validated = $request->validate([
            'review_notes' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $extension->update([
                'status' => ProjectExtension::STATUS_REJECTED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes']
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $extension->project_id,
                ProjectTimelineEvent::EVENT_EXTENSION_REJECTED,
                "Extension rejected by admin {$user->name}",
                $admin,
                [
                    'admin_name' => $user->name,
                    'requested_end_date' => $extension->new_end_date->format('Y-m-d'),
                    'review_notes' => $validated['review_notes']
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Extension rejected.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to reject extension.');
        }
    }

    /**
     * Review extension request
     */
    public function reviewExtension(Request $request, ProjectExtension $extension)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($extension->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Extension has already been reviewed.']);
        }

        $extension->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id()
        ]);

        // If approved, update project timeline
        if ($request->status === 'approved') {
            $project = $extension->project;
            if ($project->end_date) {
                $project->update([
                    'end_date' => $project->end_date->addWeeks($extension->additional_weeks),
                    'budget' => $project->budget + $extension->additional_budget
                ]);
            }
        }

        // Create timeline event
        ProjectTimelineEvent::create([
            'project_id' => $extension->project_id,
            'event_type' => $request->status === 'approved' ? 'extension_approved' : 'extension_rejected',
            'description' => "Extension request {$request->status}" . ($request->admin_notes ? ": {$request->admin_notes}" : ''),
            'user_id' => Auth::id()
        ]);

        return response()->json(['success' => true, 'message' => "Extension {$request->status} successfully!"]);
    }

    /**
     * Get extension requests partial (for AJAX loading)
     */
    public function extensionPartial(Project $project)
    {
        $extensions = $project->extensions()->with('requester')->get();

        return view('admin.projects.extensions-partial', compact('extensions'))->render();
    }

    /**
     * Show all projects (admin overview)
     */
    public function allProjects()
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $projects = Project::with(['recruiter.user', 'assignments'])
            ->latest()
            ->paginate(15);

        $title = 'All Projects';

        return view('admin.projects.all', compact('projects', 'title'));
    }

    /**
     * Project analytics dashboard
     */
    public function analytics()
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        $stats = [
            'total_projects' => Project::count(),
            'pending_approval' => Project::where('status', Project::STATUS_PENDING_APPROVAL)->count(),
            'active_projects' => Project::where('status', Project::STATUS_ACTIVE)->count(),
            'completed_projects' => Project::where('status', Project::STATUS_COMPLETED)->count(),
            'overdue_projects' => Project::where('status', Project::STATUS_OVERDUE)->count(),
            'pending_extensions' => ProjectExtension::pending()->count(),
        ];

        // Recent activity
        $recentActivity = ProjectTimelineEvent::with(['project.recruiter.user', 'triggeredBy'])
            ->latest()
            ->limit(20)
            ->get();

        $title = 'Project Analytics';

        return view('admin.projects.analytics', compact('stats', 'recentActivity', 'title'));
    }

    /**
     * Get closure request details for admin review modal
     */
    public function closureDetails(Project $project)
    {
        $user = Auth::user();
        $admin = $user->talentAdmin;

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // Ensure the project has a closure request
        if ($project->status !== 'closure_requested') {
            return response()->json([
                'success' => false,
                'message' => 'No closure request found for this project.'
            ], 404);
        }

        // Load necessary relationships
        $project->load(['recruiter.user', 'assignments.talent.user']);

        $projectData = [
            'id' => $project->id,
            'title' => $project->title,
            'status' => $project->status,
            'expected_start_date' => $project->expected_start_date ? $project->expected_start_date->format('M j, Y') : null,
            'expected_end_date' => $project->expected_end_date ? $project->expected_end_date->format('M j, Y') : null,
            'assignments_count' => $project->assignments->count(),
            'recruiter_name' => $project->recruiter ? ($project->recruiter->user->name ?? 'Unknown') : 'Unknown',
            'closure_requested_at' => $project->closure_requested_at ? $project->closure_requested_at->format('M j, Y H:i') : null,
            'closure_reason' => $project->closure_reason,
        ];

        return response()->json([
            'success' => true,
            'project' => $projectData
        ]);
    }
}
