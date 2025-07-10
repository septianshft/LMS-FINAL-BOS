<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\ProjectExtension;
use App\Models\ProjectTimelineEvent;
use App\Models\Talent;
use App\Models\TimelineConflict;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ProjectAssignmentController extends Controller
{
    /**
     * Store a newly created assignment in storage
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if ($project->status !== Project::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Can only assign talents to approved projects.');
        }

        $validated = $request->validate([
            'talent_id' => 'required|exists:talents,id',
            'role_title' => 'required|string|max:255',
            'specific_requirements' => 'nullable|string',
            'individual_budget_min' => 'nullable|numeric|min:0',
            'individual_budget_max' => 'nullable|numeric|min:0|gte:individual_budget_min',
            'start_date' => 'required|date|after_or_equal:' . $project->expected_start_date,
            'end_date' => 'required|date|after:start_date|before_or_equal:' . $project->expected_end_date,
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        try {
            DB::beginTransaction();

            $talent = Talent::findOrFail($validated['talent_id']);

            // Check for timeline conflicts
            $conflicts = $this->checkTimelineConflicts(
                $talent,
                $validated['start_date'],
                $validated['end_date']
            );

            if (!empty($conflicts)) {
                DB::rollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Timeline conflict detected. Please choose different dates or request an extension.');
            }

            // Calculate duration
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $durationDays = $startDate->diffInDays($endDate);

            $assignment = ProjectAssignment::create([
                'project_id' => $project->id,
                'talent_id' => $validated['talent_id'],
                'role_title' => $validated['role_title'],
                'specific_requirements' => $validated['specific_requirements'],
                'individual_budget_min' => $validated['individual_budget_min'],
                'individual_budget_max' => $validated['individual_budget_max'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration_days' => $durationDays,
                'priority' => $validated['priority'],
                'status' => ProjectAssignment::STATUS_ASSIGNED,
                'assigned_by' => $recruiter->id,
                'assigned_at' => now()
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_TALENT_ASSIGNED,
                "Talent {$talent->user->name} assigned to role '{$validated['role_title']}'",
                $recruiter,
                [
                    'talent_name' => $talent->user->name,
                    'role_title' => $validated['role_title'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date']
                ]
            );

            // TODO: Send notification to talent

            DB::commit();

            return redirect()->back()->with('success', 'Talent assigned successfully. They will be notified to accept the assignment.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to assign talent. Please try again.');
        }
    }

    /**
     * Accept assignment (for talents)
     */
    public function accept(ProjectAssignment $assignment)
    {
        $user = Auth::user();
        $talent = $user->talent;

        if (!$talent || $talent->id !== $assignment->talent_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if ($assignment->status !== ProjectAssignment::STATUS_ASSIGNED) {
            return redirect()->back()->with('error', 'This assignment cannot be accepted.');
        }

        try {
            DB::beginTransaction();

            $assignment->update([
                'status' => ProjectAssignment::STATUS_ACCEPTED,
                'talent_accepted_at' => now()
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $assignment->project_id,
                ProjectTimelineEvent::EVENT_TALENT_ACCEPTED,
                "Talent {$user->name} accepted assignment for role '{$assignment->role_title}'",
                $talent,
                [
                    'talent_name' => $user->name,
                    'role_title' => $assignment->role_title
                ]
            );

            // Check if project should start (all assignments accepted)
            $this->checkProjectStart($assignment->project);

            DB::commit();

            return redirect()->back()->with('success', 'Assignment accepted successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to accept assignment.');
        }
    }

    /**
     * Decline assignment (for talents)
     */
    public function decline(Request $request, ProjectAssignment $assignment)
    {
        $user = Auth::user();
        $talent = $user->talent;

        if (!$talent || $talent->id !== $assignment->talent_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if ($assignment->status !== ProjectAssignment::STATUS_ASSIGNED) {
            return redirect()->back()->with('error', 'This assignment cannot be declined.');
        }

        $validated = $request->validate([
            'decline_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $assignment->update([
                'status' => ProjectAssignment::STATUS_DECLINED,
                'decline_reason' => $validated['decline_reason'],
                'talent_declined_at' => now()
            ]);

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $assignment->project_id,
                ProjectTimelineEvent::EVENT_TALENT_DECLINED,
                "Talent {$user->name} declined assignment for role '{$assignment->role_title}'",
                $talent,
                [
                    'talent_name' => $user->name,
                    'role_title' => $assignment->role_title,
                    'decline_reason' => $validated['decline_reason']
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Assignment declined.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to decline assignment.');
        }
    }

    /**
     * Update assignment details
     */
    public function update(Request $request, ProjectAssignment $assignment)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $assignment->project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if (!in_array($assignment->status, [ProjectAssignment::STATUS_ASSIGNED, ProjectAssignment::STATUS_ACCEPTED])) {
            return redirect()->back()->with('error', 'Cannot modify this assignment.');
        }

        $validated = $request->validate([
            'role_title' => 'required|string|max:255',
            'specific_requirements' => 'nullable|string',
            'individual_budget_min' => 'nullable|numeric|min:0',
            'individual_budget_max' => 'nullable|numeric|min:0|gte:individual_budget_min',
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        try {
            $assignment->update($validated);

            return redirect()->back()->with('success', 'Assignment updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update assignment.');
        }
    }

    /**
     * Remove assignment
     */
    public function destroy(ProjectAssignment $assignment)
    {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter || $recruiter->id !== $assignment->project->recruiter_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        if ($assignment->status === ProjectAssignment::STATUS_ACTIVE) {
            return redirect()->back()->with('error', 'Cannot remove active assignment.');
        }

        try {
            DB::beginTransaction();

            // Create timeline event
            ProjectTimelineEvent::createEvent(
                $assignment->project_id,
                ProjectTimelineEvent::EVENT_TALENT_ASSIGNED,
                "Assignment removed for talent {$assignment->talent->user->name} from role '{$assignment->role_title}'",
                $recruiter,
                [
                    'talent_name' => $assignment->talent->user->name,
                    'role_title' => $assignment->role_title,
                    'action' => 'removed'
                ]
            );

            $assignment->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Assignment removed successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to remove assignment.');
        }
    }

    /**
     * Check for timeline conflicts
     */
    private function checkTimelineConflicts(Talent $talent, string $startDate, string $endDate): array
    {
        $conflicts = [];

        // Check existing assignments
        $existingAssignments = ProjectAssignment::where('talent_id', $talent->id)
            ->whereIn('status', [
                ProjectAssignment::STATUS_ASSIGNED,
                ProjectAssignment::STATUS_ACCEPTED,
                ProjectAssignment::STATUS_ACTIVE
            ])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->with('project')
            ->get();

        foreach ($existingAssignments as $assignment) {
            $conflicts[] = [
                'type' => 'assignment',
                'project' => $assignment->project->title,
                'role' => $assignment->role_title,
                'start_date' => $assignment->start_date,
                'end_date' => $assignment->end_date
            ];
        }

        return $conflicts;
    }

    /**
     * Check if project should start
     */
    private function checkProjectStart(Project $project): void
    {
        // Check if project has any assignments
        $totalAssignments = $project->assignments()->count();
        if ($totalAssignments === 0) {
            return; // No assignments to check
        }

        // Check if all assignments are accepted
        $acceptedAssignments = $project->assignments()
            ->where('status', ProjectAssignment::STATUS_ACCEPTED)
            ->count();

        $allAssignmentsAccepted = ($totalAssignments === $acceptedAssignments);

        if ($allAssignmentsAccepted && $project->status === Project::STATUS_APPROVED) {
            $project->update(['status' => Project::STATUS_ACTIVE]);

            ProjectTimelineEvent::createEvent(
                $project->id,
                ProjectTimelineEvent::EVENT_STARTED,
                "Project '{$project->title}' started - all talents have accepted their assignments",
                null,
                ['project_title' => $project->title]
            );

            // Update all assignments to active
            $project->assignments()
                ->where('status', ProjectAssignment::STATUS_ACCEPTED)
                ->update(['status' => ProjectAssignment::STATUS_ACTIVE]);
        }
    }

    /**
     * Display assignments for talents
     */    public function talentIndex(Request $request)
    {
        $user = Auth::user();
        $talent = $user->talent;
        $title = 'My Assignments';
        $roles = 'Talent';
        $assignedKelas = [];

        if (!$talent) {
            abort(403, 'Access denied. Talent profile required.');
        }

        $query = ProjectAssignment::with(['project.recruiter.user', 'project.timelineEvents'])
            ->where('talent_id', $talent->id);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(10);

        $pendingCount = ProjectAssignment::where('talent_id', $talent->id)
            ->where('status', 'pending')->count();
        $activeCount = ProjectAssignment::where('talent_id', $talent->id)
            ->where('status', 'accepted')->count();

        return view('talent.assignments.index', compact('assignments', 'pendingCount', 'activeCount', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show assignment details for talent
     */    public function talentShow(ProjectAssignment $assignment)
    {
        $user = Auth::user();
        $talent = $user->talent;
        $title = 'Assignment Details';
        $roles = 'Talent';
        $assignedKelas = [];

        if (!$talent || $assignment->talent_id !== $talent->id) {
            abort(403, 'Access denied.');
        }

        $assignment->load(['project.recruiter.user', 'project.assignments.talent.user', 'project.timelineEvents']);

        // Get progress updates (simulated - you might want to create a separate model)
        $progressUpdates = collect(); // Placeholder for progress updates

        return view('talent.assignments.show', compact('assignment', 'progressUpdates', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Respond to assignment (accept/decline)
     */
    public function respond(Request $request, ProjectAssignment $assignment)
    {
        $talent = Auth::user()->talent;

        if (!$talent || $assignment->talent_id !== $talent->id) {
            abort(403, 'Access denied.');
        }

        if ($assignment->status !== 'pending') {
            return redirect()->back()->with('error', 'Assignment has already been responded to.');
        }

        $request->validate([
            'status' => 'required|in:accepted,declined',
            'notes' => 'nullable|string|max:1000'
        ]);

        $assignment->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'responded_at' => now()
        ]);

        // Create timeline event
        ProjectTimelineEvent::create([
            'project_id' => $assignment->project_id,
            'event_type' => $request->status === 'accepted' ? 'talent_accepted' : 'talent_declined',
            'description' => "Talent {$request->status} assignment" . ($request->notes ? ": {$request->notes}" : ''),
            'user_id' => Auth::id()
        ]);

        // Check if project should be activated (all required talents accepted)
        if ($request->status === 'accepted') {
            $project = $assignment->project;
            $acceptedCount = $project->assignments()->where('status', 'accepted')->count();

            if ($acceptedCount >= $project->required_talents && $project->status === 'approved') {
                $project->update(['status' => 'active']);

                ProjectTimelineEvent::create([
                    'project_id' => $project->id,
                    'event_type' => 'project_activated',
                    'description' => 'Project activated - all required talents assigned',
                    'user_id' => Auth::id()
                ]);
            }
        }

        $message = $request->status === 'accepted'
            ? 'Assignment accepted successfully!'
            : 'Assignment declined successfully.';

        return redirect()->route('talent.assignments.index')->with('success', $message);
    }

    /**
     * Update progress for assignment
     */
    public function updateProgress(Request $request, ProjectAssignment $assignment)
    {
        $talent = Auth::user()->talent;

        if (!$talent || $assignment->talent_id !== $talent->id) {
            abort(403, 'Access denied.');
        }

        if ($assignment->status !== 'accepted') {
            return redirect()->back()->with('error', 'Can only update progress for accepted assignments.');
        }

        $request->validate([
            'progress_update' => 'required|string|max:1000',
            'completion_percentage' => 'nullable|integer|min:0|max:100'
        ]);

        // Create timeline event for progress update
        ProjectTimelineEvent::create([
            'project_id' => $assignment->project_id,
            'event_type' => 'progress_update',
            'description' => $request->progress_update,
            'user_id' => Auth::id(),
            'metadata' => $request->completion_percentage ? ['completion_percentage' => $request->completion_percentage] : null
        ]);

        return redirect()->back()->with('success', 'Progress updated successfully!');
    }

    /**
     * Request extension for project
     */
    public function requestExtension(Request $request, ProjectAssignment $assignment)
    {
        $talent = Auth::user()->talent;

        if (!$talent || $assignment->talent_id !== $talent->id) {
            abort(403, 'Access denied.');
        }

        if ($assignment->status !== 'accepted') {
            return redirect()->back()->with('error', 'Can only request extensions for accepted assignments.');
        }

        $request->validate([
            'additional_weeks' => 'required|integer|min:1|max:12',
            'additional_budget' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:1000'
        ]);

        // Check if there's already a pending extension
        $existingExtension = ProjectExtension::where('project_id', $assignment->project_id)
            ->where('status', 'pending')
            ->exists();

        if ($existingExtension) {
            return redirect()->back()->with('error', 'There is already a pending extension request for this project.');
        }

        ProjectExtension::create([
            'project_id' => $assignment->project_id,
            'requester_id' => Auth::id(),
            'additional_weeks' => $request->additional_weeks,
            'additional_budget' => $request->additional_budget ?? 0,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Create timeline event
        ProjectTimelineEvent::create([
            'project_id' => $assignment->project_id,
            'event_type' => 'extension_requested',
            'description' => "Extension requested: {$request->additional_weeks} weeks - {$request->reason}",
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Extension request submitted successfully!');
    }

    /**
     * Export talent assignments to PDF
     */
    public function exportAssignmentsPDF(Request $request)
    {
        try {
            $user = Auth::user();
            $talent = $user->talent;

            if (!$talent) {
                return redirect()->route('talent.assignments.index')
                    ->with('error', 'Profil talent tidak ditemukan.');
            }

            // Get all assignments for this talent (without pagination)
            $query = ProjectAssignment::with(['project.recruiter.user', 'project.timelineEvents'])
                ->where('talent_id', $talent->id);

            // Apply status filter if provided
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $assignments = $query->orderBy('created_at', 'desc')->get();

            // Calculate statistics
            $allAssignments = ProjectAssignment::where('talent_id', $talent->id)->get();
            $stats = [
                'total' => $allAssignments->count(),
                'pending' => $allAssignments->where('status', 'pending')->count(),
                'accepted' => $allAssignments->where('status', 'accepted')->count(),
                'declined' => $allAssignments->where('status', 'declined')->count(),
                'completed' => $allAssignments->where('status', 'completed')->count(),
            ];

            $data = [
                'user' => $user,
                'assignments' => $assignments,
                'stats' => $stats,
                'filterStatus' => $request->status ?? 'all'
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.talent.assignments-pdf', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'penugasan-saya-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('Export assignments PDF error: ' . $e->getMessage());
            return redirect()->route('talent.assignments.index')
                ->with('error', 'Gagal mengekspor penugasan ke PDF. Silakan coba lagi nanti.');
        }
    }
}
