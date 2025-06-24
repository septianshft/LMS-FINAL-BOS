<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\ProjectExtension;
use App\Models\ProjectTimelineEvent;
use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\TalentAdmin;
use App\Models\TimelineConflict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectController extends Controller
{    /**
     * Display a listing of projects for the authenticated recruiter
     */
    public function index()
    {
        $user = Auth::user();
        $title = 'My Projects';
        $roles = 'Recruiter';
        $assignedKelas = [];
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Recruiter account required.');
        }

        $projects = Project::with(['assignments.talent.user', 'timelineEvents', 'extensions'])
            ->byRecruiter($recruiter->id)
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show the form for creating a new project
     */    public function create()
    {
        $user = Auth::user();
        $title = 'Create New Project';
        $roles = 'Recruiter';
        $assignedKelas = [];
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Recruiter account required.');
        }

        return view('projects.create', compact('user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Store a newly created project in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Recruiter account required.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'industry' => 'nullable|string|max:100',
            'general_requirements' => 'nullable|string',
            'overall_budget_min' => 'nullable|numeric|min:0',
            'overall_budget_max' => 'nullable|numeric|min:0|gte:overall_budget_min',
            'expected_start_date' => 'required|date|after_or_equal:today',
            'expected_end_date' => 'required|date|after:expected_start_date',
        ]);

        try {
            DB::beginTransaction();

            // Calculate estimated duration
            $startDate = Carbon::parse($validated['expected_start_date']);
            $endDate = Carbon::parse($validated['expected_end_date']);
            $estimatedDurationDays = $startDate->diffInDays($endDate);

            $project = Project::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'industry' => $validated['industry'],
                'general_requirements' => $validated['general_requirements'],
                'overall_budget_min' => $validated['overall_budget_min'],
                'overall_budget_max' => $validated['overall_budget_max'],
                'expected_start_date' => $validated['expected_start_date'],
                'expected_end_date' => $validated['expected_end_date'],
                'estimated_duration_days' => $estimatedDurationDays,
                'status' => Project::STATUS_PENDING_APPROVAL,
                'recruiter_id' => $recruiter->id,
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_CREATED,
                "Project '{$project->title}' created by recruiter {$user->name}",
                $recruiter,
                ['project_title' => $project->title]
            );

            DB::commit();

            return redirect()->route('projects.show', $project)
                ->with('success', 'Project created successfully and submitted for admin approval.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create project. Please try again.');
        }
    }

    /**
     * Display the specified project
     */    public function show(Project $project)
    {
        $user = Auth::user();
        $title = 'Project Details';
        $roles = 'Recruiter';
        $assignedKelas = [];

        // Check access permissions
        if ($user->recruiter && $user->recruiter->id !== $project->recruiter_id) {
            return redirect()->route('projects.index')->with('error', 'Access denied.');
        }

        $project->load([
            'recruiter.user',
            'assignments.talent.user',
            'talentRequests.talent.user',
            'talentRequests.talentUser', // Add direct user relationship for talent requests
            'timelineEvents' => function($query) {
                $query->latest();
            },
            'extensions' => function($query) {
                $query->latest();
            },
            'conflicts.talent.user',
            'adminApprovedBy'
        ]);

        // Get available talents for assignment (if project is approved or active)
        // Enhanced with scouting metrics and red flag data like dashboard
        $availableTalents = collect();
        if (in_array($project->status, [Project::STATUS_APPROVED, Project::STATUS_ACTIVE])) {
            try {
                $availableTalents = Talent::with(['user', 'assignments'])
                    ->where('is_active', true)
                    ->whereHas('user', function($query) {
                        $query->whereNotNull('name')
                              ->whereNotNull('email')
                              ->where('available_for_scouting', true);
                    })
                    ->whereNotExists(function($query) use ($project) {
                        $query->select(DB::raw(1))
                              ->from('project_assignments')
                              ->whereColumn('project_assignments.talent_id', 'talents.id')
                              ->where('project_assignments.project_id', $project->id);
                    })
                    ->get()
                    ->map(function($talent) {
                        // Enhance talent data with metrics and red flag info
                        $metrics = $talent->scouting_metrics;
                        if (is_string($metrics)) {
                            $metrics = json_decode($metrics, true);
                        }

                        $talent->parsed_metrics = $metrics ?: [
                            'learning_velocity' => 0,
                            'consistency' => 0,
                            'adaptability' => 0
                        ];

                        $talent->redflag_summary = $talent->getRedflagSummary();
                        $talent->project_count = $talent->assignments->count();

                        return $talent;
                    });
            } catch (\Exception $e) {
                $availableTalents = collect();
            }
        }        return view('projects.show', compact('project', 'availableTalents', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(Project $project)
    {
        $user = Auth::user();
        $title = 'Edit Project';
        $roles = 'Recruiter';
        $assignedKelas = [];
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Recruiter account required.');
        }

        // Check access permissions
        if ($recruiter->id !== $project->recruiter_id) {
            return redirect()->route('projects.index')->with('error', 'Access denied.');
        }

        // Only allow editing if project is in draft or pending_admin status
        if (!in_array($project->status, [Project::STATUS_DRAFT, Project::STATUS_PENDING_APPROVAL])) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'This project cannot be edited in its current status.');
        }

        return view('projects.edit', compact('project', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Request extension for project
     */
    public function requestExtension(Request $request, Project $project)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $validated = $request->validate([
            'new_end_date' => 'required|date|after:' . $project->expected_end_date,
            'justification' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $extension = ProjectExtension::create([
                'project_id' => $project->id,
                'requester_type' => ProjectExtension::REQUESTER_RECRUITER,
                'requester_id' => $recruiter->id,
                'old_end_date' => $project->expected_end_date,
                'new_end_date' => $validated['new_end_date'],
                'justification' => $validated['justification'],
                'status' => ProjectExtension::STATUS_PENDING
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_EXTENSION_REQUESTED,
                "Extension requested by recruiter {$user->name} until {$validated['new_end_date']}",
                $recruiter,
                [
                    'old_end_date' => $project->expected_end_date->format('Y-m-d'),
                    'new_end_date' => $validated['new_end_date'],
                    'extension_days' => Carbon::parse($validated['new_end_date'])->diffInDays($project->expected_end_date)
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Extension request submitted successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to submit extension request.');
        }
    }

    /**
     * Update the specified project in storage
     */
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Only allow updates for pending projects
        if ($project->status !== Project::STATUS_PENDING_APPROVAL) {
            return redirect()->back()->with('error', 'Cannot edit project after approval.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'industry' => 'nullable|string|max:100',
            'general_requirements' => 'nullable|string',
            'overall_budget_min' => 'nullable|numeric|min:0',
            'overall_budget_max' => 'nullable|numeric|min:0|gte:overall_budget_min',
            'expected_start_date' => 'required|date|after_or_equal:today',
            'expected_end_date' => 'required|date|after:expected_start_date',
        ]);

        try {
            // Calculate new estimated duration
            $startDate = Carbon::parse($validated['expected_start_date']);
            $endDate = Carbon::parse($validated['expected_end_date']);
            $estimatedDurationDays = $startDate->diffInDays($endDate);

            $validated['estimated_duration_days'] = $estimatedDurationDays;

            $project->update($validated);

            return redirect()->back()->with('success', 'Project updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update project.');
        }
    }

    /**
     * Remove the specified project from storage
     */
    public function destroy(Project $project)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Only allow deletion for pending projects
        if ($project->status !== Project::STATUS_PENDING_APPROVAL) {
            return redirect()->back()->with('error', 'Cannot delete project after approval.');
        }

        try {
            DB::beginTransaction();

            // Create timeline event before deletion
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_CANCELLED,
                "Project '{$project->title}' cancelled by recruiter {$user->name}",
                $recruiter
            );

            $project->delete();

            DB::commit();

            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to delete project.');
        }
    }

    /**
     * Request project closure
     */
    public function requestClosure(Request $request, Project $project)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        // Verify access and permissions
        if (!$recruiter || $recruiter->id !== $project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Only allow closure request for active or overdue projects
        if (!in_array($project->status, [Project::STATUS_ACTIVE, Project::STATUS_OVERDUE])) {
            return redirect()->back()->with('error', 'Project closure can only be requested for active or overdue projects.');
        }

        // Check if closure is already requested
        if ($project->status === Project::STATUS_CLOSURE_REQUESTED) {
            return redirect()->back()->with('error', 'Closure has already been requested for this project.');
        }

        // Validate the request
        $validated = $request->validate([
            'closure_reason' => 'required|string|min:10|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Update project status and fields
            $project->update([
                'status' => Project::STATUS_CLOSURE_REQUESTED,
                'closure_requested_at' => now(),
                'closure_requested_by' => $user->id,
                'closure_reason' => $validated['closure_reason']
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_EXTENSION_REQUESTED, // We can reuse this or create new event type
                "Project closure requested by recruiter {$user->name}. Reason: " . $validated['closure_reason'],
                $recruiter
            );

            DB::commit();

            return redirect()->back()->with('success', 'Project closure request has been submitted and is pending admin approval.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to submit closure request. Please try again.');
        }
    }

    /**
     * Approve project closure (talent admin only)
     */
    public function approveClosure(Project $project)
    {
        $user = Auth::user();

        // This check is redundant since we have middleware, but keeping for safety
        // The middleware 'role:talent_admin' already handles this

        // Check if closure is requested
        if ($project->status !== Project::STATUS_CLOSURE_REQUESTED) {
            return redirect()->back()->with('error', 'No closure request found for this project.');
        }

        try {
            DB::beginTransaction();

            // Update project status
            $project->update([
                'status' => Project::STATUS_CANCELLED,
                'closure_approved_at' => now(),
                'closure_approved_by' => $user->id
            ]);

            // **KEY ENHANCEMENT**: Mark all associated talent requests as completed and stop time-blocking
            $associatedTalentRequests = $project->talentRequests()
                ->whereIn('status', ['onboarded', 'in_progress', 'meeting_arranged', 'agreement_reached'])
                ->get();

            foreach ($associatedTalentRequests as $talentRequest) {
                // Update the talent request status to completed
                $talentRequest->update([
                    'status' => 'completed',
                    'workflow_completed_at' => now()
                ]);

                // Stop time-blocking for the talent
                $talentRequest->stopTimeBlocking();

                // Clear talent availability cache to reflect updated status immediately
                \App\Models\TalentRequest::clearTalentAvailabilityCache($talentRequest->talent_user_id);

                // Log the automatic completion
                \Illuminate\Support\Facades\Log::info("Talent request {$talentRequest->id} automatically marked as completed due to project closure approval", [
                    'project_id' => $project->id,
                    'talent_request_id' => $talentRequest->id,
                    'talent_user_id' => $talentRequest->talent_user_id
                ]);
            }

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_COMPLETED, // We can reuse this or create new event type
                "Project closure approved by talent admin {$user->name}. {$associatedTalentRequests->count()} talent assignments completed.",
                $user,
                ['closure_approved' => true, 'completed_talent_requests' => $associatedTalentRequests->count()]
            );

            DB::commit();

            $message = "Project closure has been approved. The project is now cancelled.";
            if ($associatedTalentRequests->count() > 0) {
                $message .= " {$associatedTalentRequests->count()} talent assignment(s) have been automatically marked as completed and talents are now available for new requests.";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to approve project closure. Please try again.');
        }
    }

    /**
     * Reject project closure (talent admin only)
     */
    public function rejectClosure(Project $project)
    {
        $user = Auth::user();

        // This check is redundant since we have middleware, but keeping for safety
        // The middleware 'role:talent_admin' already handles this

        // Check if closure is requested
        if ($project->status !== Project::STATUS_CLOSURE_REQUESTED) {
            return redirect()->back()->with('error', 'No closure request found for this project.');
        }

        try {
            DB::beginTransaction();

            // Determine appropriate status to revert to
            $newStatus = $project->isOverdue() ? Project::STATUS_OVERDUE : Project::STATUS_ACTIVE;

            // Update project status
            $project->update([
                'status' => $newStatus,
                'closure_requested_at' => null,
                'closure_requested_by' => null,
                'closure_reason' => null
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_EXTENSION_REQUESTED, // We can reuse this or create new event type
                "Project closure request rejected by talent admin {$user->name}. Project status reverted to {$newStatus}.",
                $user,
                ['closure_rejected' => true, 'new_status' => $newStatus]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Project closure request has been rejected. The project remains active.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to reject project closure. Please try again.');
        }
    }
}
