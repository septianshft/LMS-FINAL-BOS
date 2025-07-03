<?php

namespace App\Http\Controllers;

use App\Models\Talent;
use App\Models\Recruiter;
use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Project;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\SmartConversionTrackingService;
use App\Services\TalentRequestNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TalentAdminController extends Controller
{
    protected $skillAnalytics;
    protected $conversionTracking;
    protected $notificationService;

    public function __construct(
        AdvancedSkillAnalyticsService $skillAnalytics,
        SmartConversionTrackingService $conversionTracking,
        TalentRequestNotificationService $notificationService
    ) {
        $this->skillAnalytics = $skillAnalytics;
        $this->conversionTracking = $conversionTracking;
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Talent Admin Dashboard';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Enhanced analytics data
        $skillAnalytics = $this->skillAnalytics->getSkillAnalytics();
        $conversionAnalytics = $this->conversionTracking->getConversionAnalytics();

        // Simplified dashboard statistics using Eloquent queries for reliability
        $dashboardStats = Cache::remember('talent_admin_dashboard_stats', 1800, function () {
            try {
                // Use simple Eloquent queries that are more reliable
                $totalTalents = Talent::count();
                $activeTalents = Talent::where('is_active', true)->count();

                // For available talents, check if they're not assigned to active projects
                $assignedTalentIds = [];
                if (Schema::hasTable('talent_assignments')) {
                    $assignedTalentIds = DB::table('talent_assignments')
                        ->join('projects', 'talent_assignments.project_id', '=', 'projects.id')
                        ->where('projects.status', 'active')
                        ->pluck('talent_assignments.talent_id')
                        ->toArray();
                }
                $availableTalents = Talent::where('is_active', true)
                    ->whereNotIn('id', $assignedTalentIds)
                    ->count();

                $totalRecruiters = Recruiter::count();
                $activeRecruiters = Recruiter::where('is_active', true)->count();
                $totalRequests = TalentRequest::count();
                $pendingRequests = TalentRequest::where('status', 'pending')->count();
                $approvedRequests = TalentRequest::where('status', 'approved')->count();

                return [
                    'totalTalents' => (int)$totalTalents,
                    'activeTalents' => (int)$activeTalents,
                    'availableTalents' => (int)$availableTalents,
                    'totalRecruiters' => (int)$totalRecruiters,
                    'activeRecruiters' => (int)$activeRecruiters,
                    'totalRequests' => (int)$totalRequests,
                    'pendingRequests' => (int)$pendingRequests,
                    'approvedRequests' => (int)$approvedRequests,
                ];
            } catch (\Exception $e) {
                Log::error('Dashboard stats query failed: ' . $e->getMessage());
                return [
                    'totalTalents' => 0,
                    'activeTalents' => 0,
                    'availableTalents' => 0,
                    'totalRecruiters' => 0,
                    'activeRecruiters' => 0,
                    'totalRequests' => 0,
                    'pendingRequests' => 0,
                    'approvedRequests' => 0,
                ];
            }
        });

        // Ensure it's always an array with default values
        if (!is_array($dashboardStats)) {
            $dashboardStats = [
                'totalTalents' => 0,
                'activeTalents' => 0,
                'availableTalents' => 0,
                'totalRecruiters' => 0,
                'activeRecruiters' => 0,
                'totalRequests' => 0,
                'pendingRequests' => 0,
                'approvedRequests' => 0,
            ];
        }

        // Get recent activity with longer cache time for better performance
        $recentActivity = Cache::remember('talent_admin_recent_activity_' . $user->id, 300, function () {
            try {
                return [
                    'latestTalents' => Talent::with(['user:id,name,email'])->latest()->take(5)->get(['id', 'user_id', 'is_active', 'created_at']),
                    'latestRecruiters' => Recruiter::with(['user:id,name,email'])->latest()->take(5)->get(['id', 'user_id', 'is_active', 'created_at']),
                    'latestRequests' => TalentRequest::with(['talent.user:id,name', 'recruiter.user:id,name'])
                        ->latest()->take(5)->get(['id', 'talent_id', 'recruiter_id', 'project_title', 'status', 'created_at']),
                    'recentProjects' => Project::with(['recruiter.user:id,name', 'assignments.talent.user:id,name'])
                        ->latest()->take(5)->get(['id', 'title', 'status', 'recruiter_id', 'overall_budget_min', 'overall_budget_max', 'estimated_duration_days', 'industry', 'created_at']),
                ];
            } catch (\Exception $e) {
                Log::error('Dashboard recent activity query failed: ' . $e->getMessage());
                return [
                    'latestTalents' => collect([]),
                    'latestRecruiters' => collect([]),
                    'latestRequests' => collect([]),
                    'recentProjects' => collect([]),
                ];
            }
        });

        // Ensure it's always an array with default collections
        if (!is_array($recentActivity)) {
            $recentActivity = [
                'latestTalents' => collect([]),
                'latestRecruiters' => collect([]),
                'latestRequests' => collect([]),
                'recentProjects' => collect([]),
            ];
        }

        // Prepare data for the view with defaults to prevent errors
        $viewData = [
            'user' => $user,
            'title' => $title,
            'roles' => $roles,
            'assignedKelas' => $assignedKelas,
            'skillAnalytics' => $skillAnalytics,
            'conversionAnalytics' => $conversionAnalytics,
            'totalTalents' => $dashboardStats['totalTalents'] ?? 0,
            'activeTalents' => $dashboardStats['activeTalents'] ?? 0,
            'availableTalents' => $dashboardStats['availableTalents'] ?? 0,
            'totalRecruiters' => $dashboardStats['totalRecruiters'] ?? 0,
            'activeRecruiters' => $dashboardStats['activeRecruiters'] ?? 0,
            'totalRequests' => $dashboardStats['totalRequests'] ?? 0,
            'pendingRequests' => $dashboardStats['pendingRequests'] ?? 0,
            'approvedRequests' => $dashboardStats['approvedRequests'] ?? 0,
            'latestTalents' => $recentActivity['latestTalents'] ?? collect([]),
            'latestRecruiters' => $recentActivity['latestRecruiters'] ?? collect([]),
            'latestRequests' => $recentActivity['latestRequests'] ?? collect([]),
            'recentProjects' => $recentActivity['recentProjects'] ?? collect([]), // This will now contain actual recent projects
        ];

        return view('talent_admin.dashboard', $viewData);
    }

    public function manageTalents()
    {
        // Remove caching for pagination to work properly
        $talents = Talent::with(['user:id,name,email,avatar'])
            ->select('id', 'user_id', 'is_active', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $title = 'Manage Talents';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.manage_talents', compact('talents', 'title', 'roles', 'assignedKelas'));
    }

    public function manageRecruiters()
    {
        $recruiters = Recruiter::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $title = 'Manage Recruiters';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.manage_recruiters', compact('recruiters', 'title', 'roles', 'assignedKelas'));
    }

    public function toggleTalentStatus(Talent $talent)
    {
        $talent->update(['is_active' => !$talent->is_active]);

        return back()->with('success', 'Talent status updated successfully.');
    }

    public function toggleRecruiterStatus(Recruiter $recruiter)
    {
        $recruiter->update(['is_active' => !$recruiter->is_active]);

        return back()->with('success', 'Recruiter status updated successfully.');
    }

    public function manageRequests(Request $request)
    {
        $query = TalentRequest::with(['recruiter.user', 'talent.user', 'project']);

        // Enhanced filter by status including acceptance states
        if ($request->filled('status')) {
            $status = $request->status;

            // Handle complex status filters
            switch ($status) {
                case 'talent_awaiting_admin':
                    // Talent accepted, awaiting admin approval
                    $query->where(function($q) {
                        $q->where('talent_accepted', true)
                          ->where('admin_accepted', false)
                          ->whereIn('status', ['pending', 'approved']);
                    });
                    break;
                case 'admin_awaiting_talent':
                    // Admin approved, awaiting talent acceptance
                    $query->where(function($q) {
                        $q->where('talent_accepted', false)
                          ->where('admin_accepted', true)
                          ->whereIn('status', ['pending', 'approved']);
                    });
                    break;
                case 'both_accepted':
                    // Both parties accepted, ready for meeting
                    $query->where('both_parties_accepted', true)
                          ->whereIn('status', ['pending', 'approved']);
                    break;
                case 'pending_review':
                    // No acceptances yet
                    $query->where('talent_accepted', false)
                          ->where('admin_accepted', false)
                          ->where('status', 'pending');
                    break;
                default:
                    // Standard status filter
                    $query->where('status', $status);
                    break;
            }
        }

        // Search functionality - properly group the OR conditions
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('recruiter.user', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhereHas('talent.user', function($subQ) use ($searchTerm) {
                    $subQ->where('name', 'LIKE', '%' . $searchTerm . '%')
                         ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                })
                ->orWhere('project_title', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('project_description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Optimize pagination size for better performance
        $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
        $requests = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Append query parameters to pagination links
        $requests->appends($request->query());

        $title = 'Manage Talent Requests';
        $roles = 'Talent Admin';
        $assignedKelas = [];
        $user = Auth::user();

        return view('admin.talent_admin.manage_requests', compact(
            'requests',
            'title',
            'roles',
            'assignedKelas',
            'user'
        ));
    }

    public function showRequest(TalentRequest $talentRequest)
    {
        $talentRequest->load(['recruiter.user', 'talent.user', 'project']);

        $title = 'Request Details';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('admin.talent_admin.request_details', compact('talentRequest', 'title', 'roles', 'assignedKelas'));
    }

    public function updateRequestStatus(Request $request, TalentRequest $talentRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,meeting_arranged,agreement_reached,onboarded,completed'
        ]);

        // Additional validation for meeting arrangement
        if ($request->status === 'meeting_arranged') {
            if (!$talentRequest->canAdminArrangeMeeting()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot arrange meeting: Both talent and admin must accept the request first.',
                    'talent_accepted' => $talentRequest->talent_accepted,
                    'admin_accepted' => $talentRequest->admin_accepted,
                    'both_parties_accepted' => $talentRequest->both_parties_accepted
                ], 400);
            }
        }

        $updateData = [
            'status' => $request->status,
        ];

        // Set timestamp based on status
        switch($request->status) {
            case 'approved':
                $updateData['approved_at'] = now();
                // Mark admin as accepted when approving
                $updateData['admin_accepted'] = true;
                $updateData['admin_accepted_at'] = now();

                // Check if talent has already accepted and mark both accepted if so
                if ($talentRequest->talent_accepted) {
                    $updateData['both_parties_accepted'] = true;
                    $updateData['workflow_completed_at'] = now();
                }
                break;
            case 'meeting_arranged':
                $updateData['meeting_arranged_at'] = now();
                // Ensure both parties are marked as accepted when meeting is arranged
                if (!$talentRequest->both_parties_accepted) {
                    $updateData['both_parties_accepted'] = true;
                    $updateData['workflow_completed_at'] = now();
                }
                break;
            case 'onboarded':
                $updateData['onboarded_at'] = now();

                // Auto-create ProjectAssignment if project_id exists and assignment doesn't exist yet
                if ($talentRequest->project_id && $talentRequest->talent_id) {
                    $existingAssignment = \App\Models\ProjectAssignment::where('project_id', $talentRequest->project_id)
                        ->where('talent_id', $talentRequest->talent_id)
                        ->first();

                    if (!$existingAssignment) {
                        try {
                            // Extract numeric budget value
                            $budgetValue = 0;
                            if ($talentRequest->budget_range) {
                                // Extract first number from budget range like "Rp 5.000.000 - Rp 15.000.000"
                                preg_match('/[\d,]+/', str_replace('.', '', $talentRequest->budget_range), $matches);
                                if (!empty($matches)) {
                                    $budgetValue = intval(str_replace(',', '', $matches[0]));
                                }
                            }

                            // Create assignment with 'accepted' status to auto-transition project
                            \App\Models\ProjectAssignment::create([
                                'project_id' => $talentRequest->project_id,
                                'talent_id' => $talentRequest->talent_id,
                                'specific_role' => $talentRequest->project_title ?? 'General Role',
                                'status' => \App\Models\ProjectAssignment::STATUS_ACCEPTED,
                                'talent_accepted_at' => now(),
                                'assignment_notes' => 'Auto-assigned from talent request onboarding',
                                'individual_budget' => $budgetValue,
                                'priority_level' => 'medium',
                                'talent_start_date' => $talentRequest->project_start_date ?? now(),
                                'talent_end_date' => $talentRequest->project_end_date ?? now()->addDays(30)
                            ]);

                            // Check if all assignments for this project are accepted and update project status
                            $this->checkAndActivateProject($talentRequest->project_id);
                        } catch (\Exception $e) {
                            // Failed to auto-create project assignment - log for debugging if needed
                            \Illuminate\Support\Facades\Log::warning('Failed to auto-create project assignment during onboarding', [
                                'talent_request_id' => $talentRequest->id,
                                'project_id' => $talentRequest->project_id,
                                'talent_id' => $talentRequest->talent_id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        // If assignment exists but not accepted, auto-accept it
                        if ($existingAssignment->status !== \App\Models\ProjectAssignment::STATUS_ACCEPTED) {
                            $existingAssignment->update([
                                'status' => \App\Models\ProjectAssignment::STATUS_ACCEPTED,
                                'talent_accepted_at' => now(),
                                'assignment_notes' => ($existingAssignment->assignment_notes ?? '') . ' - Auto-accepted during onboarding'
                            ]);

                            // Check if all assignments for this project are accepted and update project status
                            $this->checkAndActivateProject($talentRequest->project_id);
                        }
                    }
                }
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                
                // Also update corresponding ProjectAssignment status to completed
                if ($talentRequest->project_id && $talentRequest->talent_id) {
                    $existingAssignment = \App\Models\ProjectAssignment::where('project_id', $talentRequest->project_id)
                        ->where('talent_id', $talentRequest->talent_id)
                        ->first();
                    
                    if ($existingAssignment && $existingAssignment->status !== \App\Models\ProjectAssignment::STATUS_COMPLETED) {
                        $existingAssignment->update([
                            'status' => \App\Models\ProjectAssignment::STATUS_COMPLETED,
                            'completed_at' => now()
                        ]);
                    }
                }
                break;
            case 'rejected':
                // Reset acceptance flags if rejected
                $updateData['admin_accepted'] = false;
                $updateData['talent_accepted'] = false;
                $updateData['both_parties_accepted'] = false;
                break;
        }

        $talentRequest->update($updateData);

        // Handle special actions based on status
        if ($request->status === 'completed') {
            // Stop time-blocking when project is completed
            $talentRequest->stopTimeBlocking();

            // Clear talent availability cache to reflect updated status immediately
            \App\Models\TalentRequest::clearTalentAvailabilityCache($talentRequest->talent_user_id);
        }

        // Send notifications about status change
        $this->notificationService->notifyStatusChange($talentRequest, $talentRequest->getOriginal('status'), $request->status);

        $statusMessage = match($request->status) {
            'approved' => 'Request has been approved by admin. Waiting for talent acceptance to proceed to meeting arrangement.',
            'rejected' => 'Request has been rejected.',
            'meeting_arranged' => 'Meeting has been arranged successfully.',
            'agreement_reached' => 'Agreement has been reached.',
            'onboarded' => 'Talent has been onboarded successfully.',
            'completed' => 'Project has been completed.',
            default => 'Request status updated successfully.'
        };

        return response()->json([
            'success' => true,
            'message' => $statusMessage,
            'status' => $request->status,
            'both_parties_accepted' => $talentRequest->fresh()->both_parties_accepted,
            'acceptance_status' => $talentRequest->fresh()->getAcceptanceStatus(),
            'can_arrange_meeting' => $talentRequest->fresh()->canAdminArrangeMeeting()
        ]);
    }

    /**
     * Check if admin can arrange meeting for a request
     */
    public function canArrangeMeeting(TalentRequest $talentRequest)
    {
        $canArrange = $talentRequest->canAdminArrangeMeeting();

        $reason = '';
        if (!$canArrange) {
            if (!$talentRequest->talent_accepted) {
                $reason = 'Talent has not accepted the request yet';
            } elseif (!$talentRequest->admin_accepted) {
                $reason = 'Admin has not accepted the request yet';
            } elseif (!$talentRequest->both_parties_accepted) {
                $reason = 'Both parties must accept before arranging meeting';
            } elseif ($talentRequest->status !== 'approved') {
                $reason = 'Request status must be approved';
            } else {
                $reason = 'Meeting arrangement requirements not met';
            }
        }

        return response()->json([
            'canArrangeMeeting' => $canArrange,
            'reason' => $reason,
            'talent_accepted' => $talentRequest->talent_accepted,
            'admin_accepted' => $talentRequest->admin_accepted,
            'both_parties_accepted' => $talentRequest->both_parties_accepted,
            'current_status' => $talentRequest->status
        ]);
    }

    /**
     * Admin accepts a talent request (separate from approval)
     */
    public function adminAcceptRequest(Request $request, TalentRequest $talentRequest)
    {
        // Check if already accepted
        if ($talentRequest->admin_accepted) {
            return response()->json([
                'success' => false,
                'message' => 'Admin has already accepted this request.'
            ], 400);
        }

        // Check if request is in valid state for acceptance
        if ($talentRequest->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot accept a rejected request.'
            ], 400);
        }

        // Mark admin as accepted
        $oldStatus = $talentRequest->status;
        $talentRequest->markAdminAccepted();

        // Refresh to get updated data
        $talentRequest->refresh();

        // Send notifications about acceptance
        $this->notificationService->notifyStatusChange($talentRequest, $oldStatus, $talentRequest->status);

        return response()->json([
            'success' => true,
            'message' => 'Admin acceptance recorded successfully!',
            'admin_accepted' => true,
            'both_parties_accepted' => $talentRequest->both_parties_accepted,
            'acceptance_status' => $talentRequest->getAcceptanceStatus(),
            'workflow_progress' => $talentRequest->getWorkflowProgress(),
            'can_arrange_meeting' => $talentRequest->canAdminArrangeMeeting()
        ]);
    }

    /**
     * Display advanced analytics dashboard
     */
    public function analytics()
    {
        $user = Auth::user();
        $title = 'Advanced Analytics';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Get comprehensive analytics
        $skillAnalytics = $this->skillAnalytics->getSkillAnalytics();
        $conversionAnalytics = $this->conversionTracking->getConversionAnalytics();

        // Trigger smart notifications for conversion-ready users
        $notificationsSent = $this->conversionTracking->triggerSmartNotifications();

        return view('talent_admin.analytics', compact(
            'user', 'title', 'roles', 'assignedKelas',
            'skillAnalytics', 'conversionAnalytics', 'notificationsSent'
        ));
    }

    /**
     * Get conversion candidates API endpoint
     */
    public function getConversionCandidates()
    {
        $analytics = $this->conversionTracking->getConversionAnalytics();
        return response()->json($analytics['top_conversion_candidates']);
    }

    /**
     * Get skill trends API endpoint
     */
    public function getSkillTrends()
    {
        $analytics = $this->skillAnalytics->getSkillAnalytics();
        return response()->json([
            'progression_trends' => $analytics['skill_progression_trends'],
            'category_distribution' => $analytics['skill_categories'],
            'market_demand' => $analytics['market_demand_analysis']
        ]);
    }

    /**
     * Get talent details for modal view
     */
    public function getTalentDetails(Talent $talent)
    {
        try {
            // Eager load necessary relationships for a complete overview
            $talent->load([
                'user',
                'talentRequests' => function ($query) {
                    $query->with('recruiter.user:id,name')->select('id', 'recruiter_id', 'talent_user_id', 'project_title', 'status', 'created_at')->latest();
                }
            ]);

            // Transform the data to match JavaScript expectations
            $talentData = [
                'id' => $talent->id,
                'name' => $talent->user->name,
                'email' => $talent->user->email,
                'phone' => $talent->user->phone ?? null,
                'location' => $talent->user->alamat ?? null,
                'job' => $talent->user->pekerjaan ?? null,
                'is_active' => $talent->is_active,
                'avatar' => $talent->user->avatar ? asset('storage/' . $talent->user->avatar) : null,
                'joined_date' => $talent->created_at->locale('id')->translatedFormat('d F Y'),
                'skills' => $talent->user->getTalentSkillsArray() ?? [],
                'portfolio' => [], // Can be extended later if needed
            ];

            return response()->json($talentData);
        } catch (\Exception $e) {
            Log::error("Error fetching talent details for talent ID {$talent->id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving talent details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get talent details for modal view (project context)
     * For now, allows any authenticated user - should be improved with proper project access control
     */
    public function getProjectTalentDetails(Talent $talent)
    {
        try {
            // For now, just ensure user is authenticated
            // TODO: Add proper project-specific access control
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Use the existing logic for getting talent details
            return $this->getTalentDetails($talent);

        } catch (\Exception $e) {
            Log::error('Error in getProjectTalentDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch talent details'
            ], 500);
        }
    }

    /**
     * Get talent details by user ID for talent requests (project context)
     * For now, allows any authenticated user - should be improved with proper project access control
     */
    public function getProjectTalentDetailsByUserId(User $user)
    {
        try {
            // For now, just ensure user is authenticated
            // TODO: Add proper project-specific access control
            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Find talent record for this user
            $talent = Talent::where('user_id', $user->id)->first();

            if (!$talent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Talent record not found for this user'
                ], 404);
            }

            // Use the project-specific access control
            return $this->getProjectTalentDetails($talent);

        } catch (\Exception $e) {
            Log::error('Error in getProjectTalentDetailsByUserId: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch talent details'
            ], 500);
        }
    }

    /**
     * Get recruiter details for modal view
     */
    public function getRecruiterDetails(Recruiter $recruiter)
    {
        // Load recruiter with user relationship
        $recruiter->load('user');

        // Get recruitment statistics
        $totalRequests = TalentRequest::where('recruiter_id', $recruiter->id)->count();
        $approvedRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('status', 'approved')->count();
        $pendingRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('status', 'pending')->count();

        $successRate = $totalRequests > 0 ? round(($approvedRequests / $totalRequests) * 100, 1) : 0;

        $stats = [
            'total_requests' => $totalRequests,
            'approved_requests' => $approvedRequests,
            'pending_requests' => $pendingRequests,
            'success_rate' => $successRate
        ];

        // Get recent requests
        $recentRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title ?? 'Untitled Project',
                    'description' => Str::limit($request->project_description ?? 'No description', 100),
                    'status' => $request->status,
                    'created_at' => $request->created_at->locale('id')->translatedFormat('d F Y')
                ];
            });

        // Try to extract company info from user's additional fields or use defaults
        $companyName = $recruiter->user->company ?? 'Not specified';
        $jobTitle = $recruiter->user->pekerjaan ?? 'Recruiter';

        // Build company details from available user data
        $companyDetails = null;
        if ($recruiter->user->company) {
            $companyDetails = [
                'industry' => 'Not specified',
                'size' => 'Not specified',
                'website' => null,
                'description' => null
            ];
        }

        return response()->json([
            'id' => $recruiter->id,
            'name' => $recruiter->user->name,
            'email' => $recruiter->user->email,
            'phone' => $recruiter->user->phone ?? null,
            'company' => $companyName,
            'job' => $jobTitle,
            'avatar' => $recruiter->user->avatar ? asset('storage/' . $recruiter->user->avatar) : null,
            'is_active' => $recruiter->is_active,
            'joined_date' => $recruiter->created_at->locale('id')->translatedFormat('d F Y H:i'),
            'company_details' => $companyDetails,
            'stats' => $stats,
            'recent_requests' => $recentRequests
        ]);
    }

    /**
     * Manage Talent Admin Accounts
     */
    public function manageTalentAdmins()
    {
        $user = Auth::user();
        $title = 'Kelola Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        $talentAdmins = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('talent_admin.manage_talent_admins', compact('talentAdmins', 'user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show form to create new talent admin
     */
    public function createTalentAdmin()
    {
        $user = Auth::user();
        $title = 'Tambah Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('talent_admin.create_talent_admin', compact('user', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Store new talent admin
     */
    public function storeTalentAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => null, // Default to null
                'email_verified_at' => now(), // Auto-verify admin accounts
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user = User::create($userData);

            // Assign talent_admin role
            $user->assignRole('talent_admin');

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil dibuat! Akun: ' . $user->name . ' (' . $user->email . ')');

        } catch (\Exception $e) {
            Log::error('Failed to create talent admin: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Show talent admin details
     */
    public function showTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $authUser = Auth::user();
        $title = 'Detail Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        // Get admin statistics
        $stats = [
            'requests_handled' => TalentRequest::where('updated_by', $user->id)->count(),
            'talents_managed' => Talent::count(), // Could be more specific if tracking who managed whom
            'recruiters_managed' => Recruiter::count(),
            'join_date' => $user->created_at->locale('id')->translatedFormat('d F Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->locale('id')->translatedFormat('d F Y H:i') : 'Never',
        ];

        return view('talent_admin.show_talent_admin', compact('user', 'stats', 'authUser', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Show form to edit talent admin
     */
    public function editTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $authUser = Auth::user();
        $title = 'Edit Talent Admin';
        $roles = 'Talent Admin';
        $assignedKelas = [];

        return view('talent_admin.edit_talent_admin', compact('user', 'authUser', 'title', 'roles', 'assignedKelas'));
    }

    /**
     * Update talent admin
     */
    public function updateTalentAdmin(Request $request, User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->save();

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Delete talent admin
     */
    public function destroyTalentAdmin(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'User bukan Talent Admin');
        }

        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri');
        }

        try {
            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Remove role and delete user
            $user->removeRole('talent_admin');
            $user->delete();

            return redirect()->route('talent_admin.manage_talent_admins')
                ->with('success', 'Talent Admin berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus Talent Admin: ' . $e->getMessage());
        }
    }

    /**
     * Get talent admin details via AJAX
     */
    public function getTalentAdminDetails(User $user)
    {
        // Ensure the user is a talent admin
        if (!$user->hasRole('talent_admin')) {
            return response()->json(['error' => 'User bukan Talent Admin'], 404);
        }

        $stats = [
            'requests_handled' => TalentRequest::where('updated_by', $user->id)->count(),
            'talents_managed' => Talent::count(),
            'recruiters_managed' => Recruiter::count(),
            'join_date' => $user->created_at->locale('id')->translatedFormat('d F Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->locale('id')->translatedFormat('d F Y H:i') : 'Never',
        ];

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'created_at' => $user->created_at->locale('id')->translatedFormat('d F Y H:i'),
            'updated_at' => $user->updated_at->locale('id')->translatedFormat('d F Y H:i'),
            'stats' => $stats,
            // Note: We don't return password for security reasons
        ]);
    }

    /**
     * Store a newly created recruiter.
     */
    public function storeRecruiter(Request $request)
    {
        Log::info('storeRecruiter method called', [
            'request_data' => $request->all(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'json' => $request->json()->all()
        ]);

        // Handle both JSON and form data
        $inputData = $request->all();
        if (empty($inputData) && $request->isJson()) {
            $inputData = $request->json()->all();
        }

        // Log the actual input data we're working with
        Log::info('Input data to validate:', $inputData);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'pekerjaan' => 'required|string|max:255', // This aligns with public registration
            'avatar' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Optional for admin creation
            'company_name' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:100',
            'company_description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'pekerjaan.required' => 'Pekerjaan tidak boleh kosong.',
            'avatar.image' => 'Avatar harus berupa file gambar.',
            'avatar.mimes' => 'Avatar harus berformat PNG, JPG, atau JPEG.',
            'avatar.max' => 'Avatar maksimal 2MB.',
        ]);

        Log::info('Validation passed', $validatedData);

        try {
            Log::info('Starting user creation...');

            // Handle avatar upload (SAME pattern as RegisteredUserController)
            $avatarPath = 'public\images\default-avatar.png'; // Default fallback
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                Log::info('Avatar uploaded successfully', ['path' => $avatarPath]);
            }

            // Create user account (SAME pattern as RegisteredUserController)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'pekerjaan' => $request->pekerjaan, // Required field like registration
                'avatar' => $avatarPath, // Handle avatar same as registration
                'password' => Hash::make($request->password), // Use Hash::make like registration
                'email_verified_at' => now(), // Auto-verify admin-created accounts
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Assign recruiter role (SAME pattern as RegisteredUserController)
            $user->assignRole('recruiter');
            Log::info('Role assigned successfully');

            // Create recruiter profile (SAME pattern as RegisteredUserController)
            $recruiter = Recruiter::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name ?: $request->pekerjaan, // Use job as fallback
                'industry' => $request->industry ?: 'Other',
                'company_size' => $request->company_size,
                'company_description' => $request->company_description,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
            ]);

            Log::info('Recruiter profile created successfully', ['recruiter_id' => $recruiter->id]);

            return response()->json([
                'success' => true,
                'message' => 'Perekrut berhasil ditambahkan!',
                'recruiter' => [
                    'id' => $recruiter->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_name' => $recruiter->company_name,
                    'industry' => $recruiter->industry,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Data yang dimasukkan tidak valid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating recruiter: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan perekrut.',
                'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
            ], 500);
        }
    }

    /**
     * Edit a recruiter.
     */
    public function editRecruiter(Recruiter $recruiter)
    {
        $recruiter->load('user');

        return response()->json([
            'success' => true,
            'recruiter' => [
                'id' => $recruiter->id,
                'user_id' => $recruiter->user->id,
                'name' => $recruiter->user->name,
                'email' => $recruiter->user->email,
                'company_name' => $recruiter->company_name,
                'industry' => $recruiter->industry,
                'company_size' => $recruiter->company_size,
                'website' => $recruiter->website,
                'company_description' => $recruiter->company_description,
                'phone' => $recruiter->phone,
                'address' => $recruiter->address,
                'is_active' => $recruiter->is_active,
            ]
        ]);
    }

    /**
     * Update a recruiter.
     */
    public function updateRecruiter(Request $request, Recruiter $recruiter)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $recruiter->user->id,
            'company_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'company_size' => 'nullable|string|max:100',
            'company_description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Update user info
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $recruiter->user->update($userData);

            // Update recruiter profile
            $recruiter->update([
                'company_name' => $request->company_name,
                'industry' => $request->industry,
                'company_size' => $request->company_size,
                'company_description' => $request->company_description,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data perekrut berhasil diperbarui!',
                'recruiter' => [
                    'id' => $recruiter->id,
                    'name' => $recruiter->user->name,
                    'email' => $recruiter->user->email,
                    'company_name' => $recruiter->company_name,
                    'industry' => $recruiter->industry,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating recruiter: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data perekrut.',
                'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
            ], 500);
        }
    }

    /**
     * Delete a recruiter.
     */
    public function destroyRecruiter(Recruiter $recruiter)
    {
        try {
            $recruiterName = $recruiter->user->name ?? 'Unknown Recruiter';
            $userId = $recruiter->user_id;

            // Check if the recruiter has active talent requests
            $activeRequestsCount = TalentRequest::where('recruiter_id', $recruiter->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();

            if ($activeRequestsCount > 0) {
                $message = "Cannot delete recruiter '{$recruiterName}' because they have {$activeRequestsCount} active talent request(s). Please complete or cancel these requests first.";

                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }

                return redirect()->back()->with('error', $message);
            }

            // Begin transaction for data integrity
            DB::beginTransaction();

            // Update completed talent requests to remove recruiter reference
            TalentRequest::where('recruiter_id', $recruiter->id)
                ->whereIn('status', ['completed', 'cancelled'])
                ->update(['recruiter_id' => null]);

            // Delete the recruiter (this will soft-delete due to SoftDeletes trait)
            $recruiter->delete();

            // Also delete the user account
            if ($userId) {
                User::find($userId)?->delete();
            }

            DB::commit();

            $successMessage = "Perekrut '{$recruiterName}' berhasil dihapus!";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            return redirect()->route('talent_admin.manage_recruiters')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting recruiter: ' . $e->getMessage());

            $errorMessage = 'Terjadi kesalahan saat menghapus perekrut. Silakan coba lagi.';

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['general' => ['Terjadi kesalahan sistem. Silakan coba lagi.']]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Clear dashboard cache manually
     */
    public function clearDashboardCache()
    {
        $user = Auth::user();

        // Clear multiple cache keys
        Cache::forget('talent_admin_dashboard_stats');
        Cache::forget('talent_admin_recent_activity_' . $user->id);
        Cache::forget('talent_admin_recent_activity'); // Legacy cache key

        // Also clear for other admins to ensure consistency
        $this->invalidateAllAdminCaches();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache cleared successfully'
        ]);
    }

    /**
     * Invalidate recent activity cache (called when new requests are created)
     */
    public function invalidateRecentActivityCache()
    {
        // Clear cache for all admin users since we don't know who's viewing
        $this->invalidateAllAdminCaches();

        Log::info('Talent admin recent activity cache invalidated');
    }

    /**
     * Clear caches for all talent admin users
     */
    private function invalidateAllAdminCaches()
    {
        try {
            // Get all talent admin users
            $talentAdmins = User::whereHas('roles', function($query) {
                $query->where('name', 'talent_admin');
            })->get();

            // Clear user-specific cache for each admin
            foreach ($talentAdmins as $admin) {
                Cache::forget('talent_admin_recent_activity_' . $admin->id);
            }

            // Clear general cache keys
            Cache::forget('talent_admin_dashboard_stats');
            Cache::forget('talent_admin_recent_activity');

            Log::info('Cleared dashboard caches for ' . $talentAdmins->count() . ' talent admins');
        } catch (\Exception $e) {
            Log::error('Failed to clear talent admin dashboard caches: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard data for AJAX refresh
     */
    public function getDashboardData()
    {
        $user = Auth::user();

        // Get current request count
        $currentTotalRequests = TalentRequest::count();
        $currentPendingRequests = TalentRequest::where('status', 'pending')->count();

        // Get recent requests
        $recentRequests = TalentRequest::select(['id', 'project_title', 'status', 'created_at', 'recruiter_id', 'talent_user_id'])
            ->with([
                'recruiter:id,user_id',
                'recruiter.user:id,name,avatar',
                'talentUser:id,name,avatar'
            ])
            ->whereNotNull('recruiter_id')
            ->whereNotNull('talent_user_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Check if there are new requests since last check
        $lastCheckTime = session('last_dashboard_check', now()->subMinutes(1));
        $newRequestsCount = TalentRequest::where('created_at', '>', $lastCheckTime)->count();

        // Update last check time
        session(['last_dashboard_check' => now()]);

        return response()->json([
            'success' => true,
            'totalRequests' => $currentTotalRequests,
            'pendingRequests' => $currentPendingRequests,
            'newRequestsCount' => $newRequestsCount,
            'recentRequests' => $recentRequests->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title,
                    'status' => $request->status,
                    'recruiter_name' => $request->recruiter?->user?->name ?? 'Unknown',
                    'talent_name' => $request->talentUser?->name ?? 'Unknown',
                    'created_at' => $request->created_at->format('M d, Y H:i'),
                    'time_ago' => $request->created_at->diffForHumans()
                ];
            })
        ]);
    }

    /**
     * Suggest talent conversion to a trainee
     */
    public function suggestConversion(Request $request, User $user)
    {
        try {
            // Validate that the user is a trainee
            if (!$user->hasRole('trainee')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a trainee'
                ], 400);
            }

            // Check if user already has talent role
            if ($user->hasRole('talent')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a talent'
                ], 400);
            }

            // Get user's readiness score
            $readinessScore = $this->conversionTracking->calculateReadinessScore($user);

            // Validate readiness score (should be high enough for suggestion)
            if ($readinessScore < 70) {
                return response()->json([
                    'success' => false,
                    'message' => 'User readiness score is too low for conversion suggestion'
                ], 400);
            }

            // Get user's skills and course completion data
            $completedCourses = $user->courseProgress()->where('progress', 100)->count();
            $totalSkills = 0;
            if ($user->talent_skills) {
                $skills = is_string($user->talent_skills) ? json_decode($user->talent_skills, true) : $user->talent_skills;
                $totalSkills = is_array($skills) ? count($skills) : 0;
            }

            // Create conversion suggestion message
            $suggestionMessage = $this->generateConversionMessage($user, $readinessScore, $completedCourses, $totalSkills);

            // Store the suggestion notification for the user
            $this->storeConversionSuggestion($user, $suggestionMessage, $readinessScore, $completedCourses, $totalSkills);

            // Log the suggestion
            Log::info('Conversion suggestion sent', [
                'admin_id' => Auth::id(),
                'target_user_id' => $user->id,
                'readiness_score' => $readinessScore,
                'completed_courses' => $completedCourses,
                'skills_count' => $totalSkills
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversion suggestion sent successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'suggestion_data' => [
                    'readiness_score' => $readinessScore,
                    'completed_courses' => $completedCourses,
                    'skills_count' => $totalSkills,
                    'message' => $suggestionMessage
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send conversion suggestion', [
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send conversion suggestion'
            ], 500);
        }
    }

    /**
     * Generate personalized conversion message
     */
    private function generateConversionMessage(User $user, float $readinessScore, int $completedCourses, int $totalSkills): string
    {
        $name = $user->name;

        if ($readinessScore >= 90) {
            return "Hi {$name}! 🌟 You've demonstrated exceptional learning progress with {$completedCourses} completed courses and {$totalSkills} skills. You're ready to showcase your expertise as a professional talent!";
        } elseif ($readinessScore >= 80) {
            return "Hello {$name}! 🚀 Your learning journey has been impressive with {$completedCourses} courses completed and {$totalSkills} verified skills. Consider becoming a discoverable talent to connect with potential employers!";
        } else {
            return "Hi {$name}! 📈 You've made great progress with {$completedCourses} completed courses and {$totalSkills} skills. You're ready to take the next step and become a professional talent!";
        }
    }

    /**
     * Store conversion suggestion for the user
     */
    private function storeConversionSuggestion(User $user, string $message, float $readinessScore, int $completedCourses, int $totalSkills): void
    {
        // Create a session-based notification that will be shown when the user logs in
        // In a production system, you might want to store this in the database

        $suggestionData = [
            'type' => 'conversion_suggestion',
            'message' => $message,
            'reason' => "Based on your {$completedCourses} completed courses and {$totalSkills} verified skills, you're ready for professional opportunities.",
            'readiness_score' => $readinessScore,
            'skill_count' => $totalSkills,
            'course_count' => $completedCourses,
            'action_url' => route('profile.edit') . '#talent-settings',
            'suggested_by' => Auth::user()->name,
            'suggested_at' => now()->format('M d, Y H:i'),
            'expires_at' => now()->addDays(7)->format('M d, Y H:i')
        ];

        // Store in cache with user-specific key (7 days expiration)
        Cache::put("conversion_suggestion_{$user->id}", $suggestionData, now()->addDays(7));

        // Also store in session if the user is currently logged in
        if (Auth::check() && Auth::id() === $user->id) {
            session()->flash('smart_talent_suggestion', $suggestionData);
        }
    }

    /**
     * Get talent details by user ID for talent requests (original admin method)
     */
    public function getTalentDetailsByUserId(User $user)
    {
        // Find talent record for this user
        $talent = Talent::where('user_id', $user->id)->first();

        if (!$talent) {
            return response()->json([
                'success' => false,
                'message' => 'Talent record not found for this user'
            ], 404);
        }

        // Use the existing getTalentDetails logic (admin access)
        return $this->getTalentDetails($talent);
    }

    /**
     * Check if all assignments for a project are accepted and activate the project
     */
    private function checkAndActivateProject($projectId)
    {
        try {
            $project = \App\Models\Project::find($projectId);
            if (!$project) {
                return;
            }

            // Only transition from 'approved' to 'active'
            if ($project->status !== \App\Models\Project::STATUS_APPROVED) {
                return;
            }

            // Check if all project assignments are accepted
            $totalAssignments = $project->assignments()->count();
            $acceptedAssignments = $project->assignments()
                ->where('status', \App\Models\ProjectAssignment::STATUS_ACCEPTED)
                ->count();

            // If all assignments are accepted, transition project to active
            if ($totalAssignments > 0 && $acceptedAssignments === $totalAssignments) {
                $project->update([
                    'status' => \App\Models\Project::STATUS_ACTIVE
                ]);
            }
        } catch (\Exception $e) {
            // Silently handle errors to avoid disrupting the onboarding process
        }
    }

    /**
     * Manage projects for talent admins (especially closure requests)
     */
    public function manageProjects(Request $request)
    {
        $query = \App\Models\Project::with(['recruiter.user', 'assignments.talent.user'])
            ->orderBy('updated_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('recruiter.user', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $projects = $query->paginate(15);

        // Append query parameters to pagination links
        $projects->appends($request->query());

        $title = 'Manage Projects';
        $roles = 'Talent Admin';
        $assignedKelas = [];
        $user = Auth::user();

        return view('admin.talent_admin.manage_projects', compact(
            'projects',
            'title',
            'roles',
            'assignedKelas',
            'user'
        ));
    }

    public function setTalentRedflag(Request $request, Talent $talent)
    {
        $request->validate([
            'redflag_reason' => 'required|string|max:255',
        ]);

        $talent->redflagged = true;
        $talent->redflag_reason = $request->redflag_reason;
        $talent->save();

        return redirect()->back()->with('success', 'Talent has been red-flagged.');
    }

    public function unsetTalentRedflag(Talent $talent)
    {
        $talent->redflagged = false;
        $talent->redflag_reason = null;
        $talent->save();

        return redirect()->back()->with('success', 'Talent red flag has been unset.');
    }

    /**
     * Get completed projects for a talent (for redflag management)
     */
    public function getTalentCompletedProjects($talentId)
    {
        try {
            $talent = Talent::with('user')->findOrFail($talentId);

            $completedProjects = TalentRequest::with(['recruiter.user', 'redflaggedBy'])
                ->where('talent_id', $talentId)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($request) {
                    return [
                        'id' => $request->id,
                        'project_title' => $request->project_title,
                        'project_description' => $request->project_description,
                        'completed_at' => $request->updated_at->locale('id')->translatedFormat('d F Y'),
                        'recruiter_name' => $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : 'Unknown',
                        'is_redflagged' => $request->is_redflagged,
                        'redflag_reason' => $request->redflag_reason,
                        'redflagged_at' => $request->redflagged_at ? $request->redflagged_at->locale('id')->translatedFormat('d F Y') : null,
                        'redflagged_by_name' => $request->redflaggedBy ? $request->redflaggedBy->name : null
                    ];
                });

            return response()->json([
                'success' => true,
                'talent_name' => $talent->user->name,
                'completed_projects' => $completedProjects
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting completed projects: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load completed projects'
            ], 500);
        }
    }

    /**
     * Flag a specific project/request
     */
    public function flagProject(Request $request)
    {
        try {
            $request->validate([
                'project_id' => 'required|exists:talent_requests,id',
                'redflag_reason' => 'required|string|max:1000'
            ]);

            $talentRequest = TalentRequest::findOrFail($request->project_id);

            // Check if already flagged
            if ($talentRequest->is_redflagged) {
                return response()->json([
                    'success' => false,
                    'message' => 'This project is already flagged'
                ], 400);
            }

            // Only allow flagging of completed projects
            if ($talentRequest->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed projects can be flagged'
                ], 400);
            }

            // Flag the project
            $talentRequest->update([
                'is_redflagged' => true,
                'redflag_reason' => $request->redflag_reason,
                'redflagged_at' => now(),
                'redflagged_by' => Auth::id()
            ]);

            Log::info('Project flagged', [
                'project_id' => $talentRequest->id,
                'talent_id' => $talentRequest->talent_id,
                'flagged_by' => Auth::id(),
                'reason' => $request->redflag_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project flagged successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error flagging project: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to flag project'
            ], 500);
        }
    }

    /**
     * Get redflag history for a talent (for talent admin)
     */
    public function getTalentRedflagHistory($talentId)
    {
        try {
            $talent = Talent::with('user')->findOrFail($talentId);

            // Get all completed talent requests for this talent with redflag information
            $completedRequests = TalentRequest::with(['recruiter.user', 'redflaggedBy'])
                ->where('talent_id', $talentId)
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get red-flagged projects
            $redflaggedProjects = $completedRequests->where('is_redflagged', true)->map(function($request) {
                return [
                    'id' => $request->id,
                    'project_title' => $request->project_title,
                    'project_description' => $request->project_description,
                    'redflag_reason' => $request->redflag_reason,
                    'redflagged_at' => $request->redflagged_at ? $request->redflagged_at->locale('id')->translatedFormat('d F Y') : null,
                    'redflagged_by_name' => $request->redflaggedBy ? $request->redflaggedBy->name : 'Unknown',
                    'recruiter_name' => $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : 'Unknown'
                ];
            })->values();

            // Calculate summary
            $redflagSummary = $talent->getRedflagSummary();

            return response()->json([
                'success' => true,
                'talent_name' => $talent->user->name,
                'redflag_summary' => $redflagSummary,
                'redflagged_projects' => $redflaggedProjects,
                'total_completed' => $completedRequests->count(),
                'total_redflagged' => $redflaggedProjects->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Redflag history error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load redflag history'
            ], 500);
        }
    }

    /**
     * Export talent requests summary report
     */
    public function exportRequestsSummary(Request $request)
    {
        try {
            // Apply the same filters as the manage requests page
            $query = TalentRequest::with([
                'talent.user',
                'recruiter.user',
                'project'
            ]);

            // Apply status filter
            if ($request->filled('status')) {
                $status = $request->get('status');

                switch ($status) {
                    case 'pending_review':
                        $query->where('status', 'pending');
                        break;
                    case 'talent_awaiting_admin':
                        $query->where('talent_accepted', true)->where('admin_accepted', false);
                        break;
                    case 'admin_awaiting_talent':
                        $query->where('admin_accepted', true)->where('talent_accepted', false);
                        break;
                    case 'both_accepted':
                        $query->where('talent_accepted', true)->where('admin_accepted', true);
                        break;
                    default:
                        $query->where('status', $status);
                        break;
                }
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('recruiter.user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('talent.user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $requests = $query->orderBy('created_at', 'desc')->get();

            // Calculate summary statistics
            $stats = [
                'total_requests' => $requests->count(),
                'pending_requests' => $requests->where('status', 'pending')->count(),
                'approved_requests' => $requests->where('status', 'approved')->count(),
                'completed_requests' => $requests->where('status', 'completed')->count(),
                'rejected_requests' => $requests->where('status', 'rejected')->count(),
                'talent_awaiting_admin' => $requests->where('talent_accepted', true)->where('admin_accepted', false)->count(),
                'admin_awaiting_talent' => $requests->where('admin_accepted', true)->where('talent_accepted', false)->count(),
                'both_accepted' => $requests->where('talent_accepted', true)->where('admin_accepted', true)->count(),
            ];

            $data = [
                'title' => 'Talent Requests Summary Report',
                'subtitle' => 'Comprehensive overview of talent acquisition requests',
                'exportDate' => now(),
                'generatedBy' => Auth::user()->name,
                'filters' => [
                    'status' => $request->get('status'),
                    'search' => $request->get('search'),
                    'applied_at' => now()
                ],
                'requests' => $requests,
                'stats' => $stats
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.talent_admin.requests-summary', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'talent-requests-summary-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Export requests summary error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export requests summary report.');
        }
    }

    /**
     * Export detailed talent requests report
     */
    public function exportRequestsDetailed(Request $request)
    {
        try {
            $query = TalentRequest::with([
                'talent.user',
                'recruiter.user',
                'project',
                'talent.assignments',
                'talent.talentRequests'
            ]);

            // Apply status filter
            if ($request->filled('status')) {
                $status = $request->get('status');

                switch ($status) {
                    case 'pending_review':
                        $query->where('status', 'pending');
                        break;
                    case 'talent_awaiting_admin':
                        $query->where('talent_accepted', true)->where('admin_accepted', false);
                        break;
                    case 'admin_awaiting_talent':
                        $query->where('admin_accepted', true)->where('talent_accepted', false);
                        break;
                    case 'both_accepted':
                        $query->where('talent_accepted', true)->where('admin_accepted', true);
                        break;
                    default:
                        $query->where('status', $status);
                        break;
                }
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('recruiter.user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('talent.user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $requests = $query->orderBy('created_at', 'desc')->get();

            // Enrich requests with additional data
            $enrichedRequests = $requests->map(function($request) {
                $talentMetrics = null;
                if ($request->talent && !empty($request->talent->scouting_metrics)) {
                    if (is_string($request->talent->scouting_metrics)) {
                        $talentMetrics = json_decode($request->talent->scouting_metrics, true);
                    } else {
                        $talentMetrics = $request->talent->scouting_metrics;
                    }
                }

                return [
                    'id' => $request->id,
                    'recruiter_name' => $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : 'Unknown',
                    'recruiter_company' => $request->recruiter ? $request->recruiter->company_name : 'Unknown',
                    'talent_name' => $request->talent && $request->talent->user ? $request->talent->user->name : 'Unknown',
                    'talent_email' => $request->talent && $request->talent->user ? $request->talent->user->email : 'Unknown',
                    'status' => $request->getUnifiedDisplayStatus(),
                    'status_raw' => $request->status,
                    'talent_accepted' => $request->talent_accepted,
                    'admin_accepted' => $request->admin_accepted,
                    'project_title' => $request->project ? $request->project->title : ($request->project_title ?? 'N/A'),
                    'project_end_date' => $request->project ? ($request->project->expected_end_date ? $request->project->expected_end_date->locale('id')->translatedFormat('d F Y') : 'N/A') : ($request->project_end_date ? $request->project_end_date->locale('id')->translatedFormat('d F Y') : 'N/A'),
                    'created_at' => $request->created_at->locale('id')->translatedFormat('d F Y H:i'),
                    'updated_at' => $request->updated_at->locale('id')->translatedFormat('d F Y H:i'),
                    'talent_project_count' => $request->talent ? $request->talent->assignments->count() : 0,
                    'talent_redflag_count' => $request->talent ? $request->talent->getRedflagCount() : 0,
                    'talent_metrics' => $talentMetrics,
                    'workflow_completed_at' => $request->workflow_completed_at ? $request->workflow_completed_at->locale('id')->translatedFormat('d F Y H:i') : null
                ];
            });

            $data = [
                'title' => 'Detailed Talent Requests Report',
                'subtitle' => 'Comprehensive detailed analysis of talent acquisition requests',
                'exportDate' => now(),
                'generatedBy' => Auth::user()->name,
                'filters' => [
                    'status' => $request->get('status'),
                    'search' => $request->get('search'),
                    'applied_at' => now()
                ],
                'requests' => $enrichedRequests,
                'total_count' => $enrichedRequests->count()
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.talent_admin.requests-detailed', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'talent-requests-detailed-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Export detailed requests error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export detailed requests report.');
        }
    }
}
