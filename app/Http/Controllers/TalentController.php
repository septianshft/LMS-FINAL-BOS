<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\TalentRequest;
use App\Models\User;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\TalentRequestNotificationService;
use Carbon\Carbon;
use Exception;

class TalentController extends Controller
{
    protected $skillAnalytics;
    protected $notificationService;

    public function __construct(AdvancedSkillAnalyticsService $skillAnalytics, TalentRequestNotificationService $notificationService)
    {
        $this->skillAnalytics = $skillAnalytics;
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Dashboard';
        $roles = 'Talent';
        $assignedKelas = [];

        // Get talent data for redflag status
        $talent = $user->talent;

        // Get real talent statistics
        $talentStats = $this->getTalentDashboardStats($user);

        // Get skill analytics
        $skillAnalytics = $this->skillAnalytics->getSkillAnalytics();

        // Get user's actual skills and progress
        $userSkills = $this->getUserSkillProgress($user);

        // Get recent talent requests (applications)
        $recentRequests = $this->getRecentTalentRequests($user);

        // Get job opportunities (from recruiters seeking talents)
        $jobOpportunities = $this->getJobOpportunities();

        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);

        // Get profile completeness
        $profileCompleteness = $this->calculateProfileCompleteness($user);

        // Get job history (accepted/completed collaborations)
        $jobHistory = $this->getJobHistory($user);

        return view('admin.talent.dashboard', compact(
            'user', 'title', 'roles', 'assignedKelas', 'talent',
            'talentStats', 'skillAnalytics', 'userSkills',
            'recentRequests', 'jobOpportunities', 'recentActivity',
            'profileCompleteness', 'jobHistory'
        ));
    }

    private function getTalentDashboardStats($user)
    {
        // Count actual applications/requests
        $totalApplications = TalentRequest::whereHas('talent', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        $pendingApplications = TalentRequest::whereHas('talent', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->count();

        $approvedApplications = TalentRequest::whereHas('talent', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'approved')->count();

        // Count completed collaborations
        $completedCollaborations = TalentRequest::whereHas('talent', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('both_parties_accepted', true)->count();

        // Count messages (for now, simulated - could be connected to real messaging system)
        $newMessages = 5; // This could be connected to a real messaging system

        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'completed_collaborations' => $completedCollaborations,
            'new_messages' => $newMessages,
        ];
    }

    private function getUserSkillProgress($user)
    {
        // Get user's actual skills using the simplified method
        $skills = [];

        // Use the getTalentSkillsArray() method which handles the new structure properly
        $userSkills = $user->getTalentSkillsArray();

        if (!empty($userSkills)) {
            foreach ($userSkills as $skill) {
                if (is_array($skill)) {
                    // Convert proficiency to level number for dashboard display
                    $proficiencyToLevel = [
                        'beginner' => 1,
                        'intermediate' => 3,
                        'advanced' => 5
                    ];

                    $proficiency = $skill['proficiency'] ?? 'intermediate';
                    $level = $proficiencyToLevel[$proficiency] ?? 3;

                    $skills[] = [
                        'name' => $skill['skill_name'] ?? 'Unknown Skill',
                        'level' => $level,
                        'percentage' => min(100, $level * 20) // Convert level to percentage
                    ];
                }
            }
        }

        // If no skills, add some default based on course completions
        if (empty($skills)) {
            $completedCourses = $user->courseProgress()->where('progress', 100)->count();
            if ($completedCourses > 0) {
                $skills = [
                    ['name' => 'General Knowledge', 'level' => min(5, $completedCourses), 'percentage' => min(100, $completedCourses * 20)],
                ];
            }
        }

        return $skills;
    }

    private function getRecentTalentRequests($user)
    {
        return TalentRequest::with(['recruiter.user'])
            ->whereHas('talent', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();
    }

    private function getJobOpportunities()
    {
        $user = Auth::user();

        // Get talent requests specifically targeting the logged-in user's talent profile
        return TalentRequest::with(['recruiter.user'])
            ->whereHas('talent.user', function($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->whereIn('status', ['pending', 'approved'])
            ->where('talent_accepted', false) // Only show if talent hasn't accepted yet
            ->latest()
            ->take(6)
            ->get()
            ->map(function($request) {
                return [
                    'request_id' => $request->id,
                    'id' => $request->id,
                    'title' => $request->project_title ?? 'Project Opportunity',
                    'company' => $request->recruiter->user->name ?? 'Company',
                    'budget' => $request->budget_range ?? 'Budget TBD',
                    'duration' => $request->project_duration ?? 'Duration TBD',
                    'posted_date' => $request->created_at,
                    'description' => $request->project_description ?? '',
                    'requirements' => $request->requirements ?? '',
                    'project_description' => $request->project_description ?? '',
                    'recruiter_name' => $request->recruiter->user->name ?? 'Unknown',
                    'talent_accepted' => $request->talent_accepted ?? false,
                    'admin_accepted' => $request->admin_accepted ?? false,
                    'both_parties_accepted' => $request->both_parties_accepted ?? false,
                    'acceptance_status' => $request->getTalentFriendlyAcceptanceStatus(),
                    'is_pre_approved' => $request->isPreApproved(),
                    'workflow_progress' => $request->getWorkflowProgress(),
                    'can_accept' => !$request->talent_accepted && ($request->status === 'pending' || $request->status === 'approved'),
                    'can_reject' => $request->status === 'pending' || $request->status === 'approved'
                ];
            });
    }

    private function getRecentActivity($user)
    {
        $activities = [];

        // Add recent course completions
        $recentCourses = $user->courseProgress()
            ->where('progress', 100)
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->latest('updated_at')
            ->take(3)
            ->get();

        foreach ($recentCourses as $course) {
            $activities[] = [
                'type' => 'course_completed',
                'title' => 'Course completed: ' . ($course->course->title ?? 'Unknown Course'),
                'time' => $course->updated_at->diffForHumans(),
                'icon' => 'fas fa-graduation-cap',
                'color' => 'green'
            ];
        }

        // Add recent talent requests
        $recentRequests = TalentRequest::whereHas('talent', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->take(2)->get();

        foreach ($recentRequests as $request) {
            $activities[] = [
                'type' => 'request_received',
                'title' => 'Request received: ' . $request->project_title,
                'time' => $request->created_at->diffForHumans(),
                'icon' => 'fas fa-handshake',
                'color' => 'blue'
            ];
        }

        // Sort by time and return latest 5
        return collect($activities)->sortByDesc('time')->take(5)->values();
    }

    private function calculateProfileCompleteness($user)
    {
        $completeness = 0;
        $maxScore = 100;

        // Basic profile info (40 points)
        if ($user->name) $completeness += 10;
        if ($user->email) $completeness += 10;
        if ($user->pekerjaan) $completeness += 10;
        if ($user->avatar && $user->avatar !== 'images/default-avatar.svg') $completeness += 10; // Only custom avatars count

        // Skills (30 points)
        $userSkills = $user->getTalentSkillsArray();
        if (!empty($userSkills)) {
            $completeness += 30;
        }

        // Course progress (20 points)
        $completedCourses = $user->courseProgress()->where('progress', 100)->count();
        if ($completedCourses > 0) {
            $completeness += min(20, $completedCourses * 5); // 5 points per course, max 20
        }

        // Scouting availability (10 points)
        if ($user->available_for_scouting) $completeness += 10;

        return min(100, $completeness);
    }

    private function getJobHistory($user)
    {
        // Get accepted and completed talent requests
        return TalentRequest::with(['recruiter.user'])
            ->whereHas('talent.user', function($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->where(function($query) {
                // Include requests that are either:
                // 1. Accepted by talent (regardless of admin status)
                // 2. Both parties accepted (completed workflow)
                // 3. Any approved/completed status
                $query->where('talent_accepted', true)
                      ->orWhere('both_parties_accepted', true)
                      ->orWhereIn('status', ['approved', 'completed', 'in_progress']);
            })
            ->orderBy('talent_accepted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10) // Show last 10 collaborations
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title ?? 'Untitled Project',
                    'company' => $request->recruiter->user->name ?? 'Unknown Company',
                    'company_role' => $request->recruiter->user->pekerjaan ?? 'Company',
                    'budget_range' => $request->budget_range ?? 'Budget TBD',
                    'project_duration' => $request->project_duration ?? 'Duration TBD',
                    'project_description' => $request->project_description ?? '',
                    'status' => $request->status,
                    'talent_accepted_at' => $request->talent_accepted_at,
                    'workflow_completed_at' => $request->workflow_completed_at,
                    'created_at' => $request->created_at,
                    'is_completed' => $request->both_parties_accepted,
                    'is_in_progress' => $request->talent_accepted && !$request->both_parties_accepted,
                    'formatted_status' => $request->getTalentFriendlyAcceptanceStatus(),
                    'status_color' => $this->getStatusColor($request),
                    'duration_worked' => $request->talent_accepted_at
                        ? ($request->workflow_completed_at
                            ? $request->talent_accepted_at->diffInDays($request->workflow_completed_at) . ' days'
                            : $request->talent_accepted_at->diffInDays(now()) . ' days (ongoing)')
                        : 'Not started'
                ];
            });
    }

    private function getStatusColor($request)
    {
        if ($request->both_parties_accepted) {
            return 'green'; // Completed
        } elseif ($request->talent_accepted && $request->admin_accepted) {
            return 'blue'; // In progress
        } elseif ($request->talent_accepted) {
            return 'yellow'; // Waiting for admin
        } elseif ($request->status === 'approved') {
            return 'purple'; // Approved
        }
        return 'gray'; // Default
    }



    /**
     * Get talent's pending requests with optimization
     */
    public function getMyRequests()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $requests = TalentRequest::with(['recruiter.user'])
                ->whereHas('talent.user', function($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->take(50) // Limit results for better performance
                ->get()
                ->map(function($request) {
                    try {
                        return [
                            'id' => $request->id,
                            'project_title' => $request->project_title ?? 'Untitled Project',
                            'project_description' => $request->project_description ?? 'No description provided',
                            'recruiter_name' => $request->recruiter?->user?->name ?? 'Unknown Recruiter',
                            'recruiter_company' => $request->recruiter?->user?->pekerjaan ?? 'No company specified',
                            'budget_range' => $request->budget_range ?? 'Not specified',
                            'project_duration' => $request->project_duration ?? 'Not specified',
                            'status' => $request->status ?? 'pending',
                            'formatted_status' => $request->getUnifiedDisplayStatus() ?? 'Pending',
                            'status_badge_color' => $request->getStatusBadgeColorClasses() ?? 'bg-gray-100 text-gray-800',
                            'status_icon' => $request->getStatusIcon() ?? 'fas fa-clock',
                            'talent_accepted' => $request->talent_accepted ?? false,
                            'admin_accepted' => $request->admin_accepted ?? false,
                            'both_parties_accepted' => $request->both_parties_accepted ?? false,
                            'acceptance_status' => $request->getAcceptanceStatus() ?? 'Pending review',
                            'workflow_progress' => $request->getWorkflowProgress() ?? 0,
                            'created_at' => $request->created_at->format('M d, Y H:i'),
                            'can_accept' => !$request->talent_accepted && in_array($request->status, ['pending', 'approved']),
                            'can_reject' => in_array($request->status, ['pending', 'approved'])
                        ];
                    } catch (Exception $e) {
                        // Log individual request errors but continue processing
                        Log::error('Error processing talent request ' . $request->id . ': ' . $e->getMessage());
                        return [
                            'id' => $request->id ?? 0,
                            'project_title' => 'Error loading project',
                            'project_description' => 'There was an error loading this request.',
                            'recruiter_name' => 'Unknown',
                            'recruiter_company' => 'Unknown',
                            'budget_range' => 'Unknown',
                            'project_duration' => 'Unknown',
                            'status' => 'error',
                            'formatted_status' => 'Error',
                            'status_badge_color' => 'bg-red-100 text-red-800',
                            'status_icon' => 'fas fa-exclamation-triangle',
                            'talent_accepted' => false,
                            'admin_accepted' => false,
                            'both_parties_accepted' => false,
                            'acceptance_status' => 'Error loading status',
                            'workflow_progress' => 0,
                            'created_at' => 'Unknown',
                            'can_accept' => false,
                            'can_reject' => false
                        ];
                    }
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);

        } catch (Exception $e) {
            Log::error('Error in getMyRequests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading your requests. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function myRequests()
    {
        $userId = Auth::id();
        $user = User::with('talent')->find($userId);
        $title = 'My Requests';
        $roles = 'Talent';
        $assignedKelas = [];
        $talent = $user->talent;

        $requests = collect();
        if ($talent) {
            $requests = TalentRequest::with(['recruiter.user'])
                ->where('talent_id', $talent->id)
                ->latest()
                ->paginate(10);
        }

        return view('admin.talent.requests', compact('user', 'title', 'roles', 'assignedKelas', 'requests'));
    }

    /**
     * Get detailed information about a specific talent request
     */
    public function getRequestDetails(TalentRequest $talentRequest)
    {
        try {
            $user = Auth::user();
            $talent = $user->talent;

            // Ensure the request belongs to the authenticated talent
            if (!$talent || $talentRequest->talent_id !== $talent->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to this request'
                ], 403);
            }

            // Load relationships with error handling
            $talentRequest->load([
                'recruiter.user',
                'talent.user'
            ]);

            // Format the response with detailed information and null checks
            $requestDetails = [
                'id' => $talentRequest->id,
                'title' => $talentRequest->project_title ?? 'Collaboration Request',
                'description' => $talentRequest->project_description ?? 'No description provided',
                'budget_range' => $talentRequest->budget_range ?? 'Not specified',
                'project_duration' => $talentRequest->project_duration ?? 'Not specified',
                'collaboration_type' => $talentRequest->collaboration_type ?? 'General',
                'status' => $talentRequest->status ?? 'pending',
                'display_status' => $talentRequest->getDisplayStatus() ?? 'Pending',
                'acceptance_status' => $talentRequest->getTalentFriendlyAcceptanceStatus() ?? 'Pending review',
                'talent_accepted' => $talentRequest->talent_accepted ?? false,
                'admin_accepted' => $talentRequest->admin_accepted ?? false,
                'both_parties_accepted' => $talentRequest->both_parties_accepted ?? false,
                'can_accept' => !$talentRequest->talent_accepted && in_array($talentRequest->status, ['pending', 'approved']),
                'can_reject' => in_array($talentRequest->status, ['pending', 'approved']),
                'workflow_progress' => $talentRequest->getWorkflowProgress() ?? [],
                'submitted_at' => $talentRequest->created_at ? $talentRequest->created_at->format('M d, Y \a\t h:i A') : 'Unknown',
                'updated_at' => $talentRequest->updated_at ? $talentRequest->updated_at->format('M d, Y \a\t h:i A') : 'Unknown',
                'recruiter' => [
                    'name' => $talentRequest->recruiter?->user?->name ?? 'Unknown',
                    'email' => $talentRequest->recruiter?->user?->email ?? 'No email',
                    'company' => $talentRequest->recruiter?->company_name ??
                               $talentRequest->recruiter?->user?->pekerjaan ?? 'No company specified',
                    'avatar' => $talentRequest->recruiter?->user?->avatar ?? null
                ]
            ];

            return response()->json(['success' => true, 'request' => $requestDetails]);

        } catch (Exception $e) {
            Log::error('Error in getRequestDetails for request ' . $talentRequest->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load request details. Please try again later.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Accept a talent request
     */
    public function acceptRequest(Request $request, TalentRequest $talentRequest)
    {
        try {
            $user = Auth::user();
            $talent = $user->talent;

            // Ensure the request belongs to the authenticated talent
            if (!$talent || $talentRequest->talent_id !== $talent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this request'
                ], 403);
            }

            // Check if request can be accepted
            if ($talentRequest->talent_accepted) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already accepted this request'
                ], 400);
            }

            if (!in_array($talentRequest->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request cannot be accepted at this time'
                ], 400);
            }

            // Mark talent as accepted
            $oldStatus = $talentRequest->status;
            $talentRequest->markTalentAccepted();

            // Refresh to get updated data
            $talentRequest->refresh();

            // Send notifications about acceptance
            $this->notificationService->notifyStatusChange($talentRequest, $oldStatus, $talentRequest->status);

            return response()->json([
                'success' => true,
                'message' => 'Request accepted successfully! Both parties have now accepted.',
                'talent_accepted' => true,
                'both_parties_accepted' => $talentRequest->both_parties_accepted,
                'acceptance_status' => $talentRequest->getAcceptanceStatus(),
                'workflow_progress' => $talentRequest->getWorkflowProgress()
            ]);

        } catch (Exception $e) {
            Log::error('Error accepting talent request ' . $talentRequest->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while accepting the request. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject a talent request
     */
    public function rejectRequest(Request $request, TalentRequest $talentRequest)
    {
        try {
            $user = Auth::user();
            $talent = $user->talent;

            // Ensure the request belongs to the authenticated talent
            if (!$talent || $talentRequest->talent_id !== $talent->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this request'
                ], 403);
            }

            // Check if request can be rejected
            if (!in_array($talentRequest->status, ['pending', 'approved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This request cannot be rejected at this time'
                ], 400);
            }

            // Update request status to rejected
            $oldStatus = $talentRequest->status;
            $talentRequest->update([
                'status' => 'rejected',
                'talent_accepted' => false,
                'admin_accepted' => false,
                'both_parties_accepted' => false,
            ]);

            // Send notifications about rejection
            $this->notificationService->notifyStatusChange($talentRequest, $oldStatus, 'rejected');

            return response()->json([
                'success' => true,
                'message' => 'Request declined successfully.',
                'status' => 'rejected'
            ]);

        } catch (Exception $e) {
            Log::error('Error rejecting talent request ' . $talentRequest->id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while declining the request. Please try again.'
            ], 500);
        }
    }
}
