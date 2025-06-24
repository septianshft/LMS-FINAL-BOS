<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use App\Services\TalentScoutingService;
use App\Services\TalentMatchingService;
use App\Services\AdvancedSkillAnalyticsService;
use App\Services\TalentRequestNotificationService;

class RecruiterController extends Controller
{
    protected $scoutingService;
    protected $analyticsService;
    protected $notificationService;
    protected $matchingService;

    public function __construct(
        TalentScoutingService $scoutingService,
        AdvancedSkillAnalyticsService $analyticsService,
        TalentRequestNotificationService $notificationService,
        TalentMatchingService $matchingService
    ) {
        $this->scoutingService = $scoutingService;
        $this->analyticsService = $analyticsService;
        $this->notificationService = $notificationService;
        $this->matchingService = $matchingService;
    }

    public function dashboard()
    {
        $userId = Auth::id();
        $user = User::with('recruiter')->find($userId);
        $title = 'Recruiter Dashboard';
        $roles = 'Recruiter';
        $assignedKelas = [];

        // Get current recruiter
        $recruiter = $user->recruiter;

        // Initialize collections with safe defaults
        $talents = collect();
        $myRequests = collect();
        $topTalents = collect();
        $analytics = [];
        $dashboardStats = [];

        // Only proceed if user has a recruiter profile
        if ($recruiter) {
            // Get active talents for discovery with optimized queries
            $talents = Talent::with(['user', 'talentRequests' => function($query) use ($recruiter) {
                $query->where('recruiter_id', $recruiter->id ?? 0);
            }])
                ->where('is_active', true)
                ->whereHas('user', function($query) {
                    $query->whereNotNull('name')
                          ->whereNotNull('email')
                          ->where('available_for_scouting', true);
                })
                ->latest()
                ->paginate(12);

            // Optimize: Batch load availability status to prevent N+1 queries
            $talentIds = $talents->getCollection()->pluck('user_id');
            $availabilityCache = [];

            // Pre-calculate availability for all talents
            foreach ($talentIds as $userId) {
                $availabilityCache[$userId] = $this->matchingService->isTalentAvailable($userId);
            }

            // Add scouting metrics, availability status, and completed projects to each talent
            $talents->getCollection()->transform(function ($talent) use ($availabilityCache) {
                // Always load scouting metrics for dashboard display
                // First check for cached metrics
                $cacheKey = "talent_metrics_{$talent->id}";
                $cachedMetrics = cache()->get($cacheKey);

                if ($cachedMetrics) {
                    $talent->scouting_metrics = $cachedMetrics;
                } else {
                    // Calculate and cache metrics if not found
                    $metrics = $this->scoutingService->getTalentScoutingMetrics($talent);
                    $talent->scouting_metrics = $metrics;
                    cache()->put($cacheKey, $metrics, now()->addHours(24));

                    // Save only the scouting_metrics to database for persistence
                    // Use update() to save only specific columns
                    $talent->update(['scouting_metrics' => $metrics]);
                }

                // Get completed projects for this talent
                $completedProjects = TalentRequest::with(['recruiter.user'])
                    ->where('talent_id', $talent->id)
                    ->where('status', 'completed')
                    ->orderBy('updated_at', 'desc')
                    ->get()
                    ->map(function($request) {
                        // Calculate duration
                        $duration = 'Not specified';
                        if ($request->project_start_date && $request->project_end_date) {
                            $days = $request->project_start_date->diffInDays($request->project_end_date);
                            if ($days < 30) {
                                $duration = $days . ' days';
                            } else {
                                $months = round($days / 30, 1);
                                $duration = $months . ' months';
                            }
                        } elseif ($request->project_duration) {
                            $duration = $request->project_duration;
                        }

                        return [
                            'title' => $request->project_title,
                            'type' => 'Project',
                            'status' => 'Completed',
                            'date' => $request->updated_at->format('M d, Y'),
                            'recruiter' => $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : 'Unknown',
                            'duration' => $duration,
                            'industry' => $request->recruiter && $request->recruiter->industry ? $request->recruiter->industry : 'Not specified',
                            'is_redflagged' => $request->is_redflagged ?? false
                        ];
                    })->toArray();

                $talent->completed_projects = $completedProjects;

                // Use cached availability to avoid repeated database calls
                // Set this after saving to avoid saving it to database
                $talent->availability_status = $availabilityCache[$talent->user_id] ?? ['available' => false];

                return $talent;
            });

            // Get my talent requests summary with eager loading
            $myRequests = TalentRequest::with(['talent.user'])
                ->where('recruiter_id', $recruiter->id)
                ->latest()
                ->take(5)
                ->get();

            // Cache top talents and analytics for better performance
            $topTalents = cache()->remember("top_talents_{$recruiter->id}", 300, function() {
                return $this->scoutingService->getTopTalents(6);
            });

            $analytics = cache()->remember("recruiter_analytics_{$recruiter->id}", 600, function() {
                return $this->analyticsService->getSkillAnalytics();
            });

            // Get recruiter-specific dashboard statistics
            $dashboardStats = $this->getRecruiterDashboardStats($recruiter);

        } else {
            // For users without recruiter profile, create empty paginated collection
            $talents = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 12, 1, ['path' => request()->url()]
            );
        }

        return view('admin.recruiter.dashboard', compact(
            'user', 'title', 'roles', 'assignedKelas', 'talents',
            'myRequests', 'recruiter', 'topTalents', 'analytics', 'dashboardStats'
        ));
    }

    /**
     * Get recruiter-specific dashboard statistics
     */
    private function getRecruiterDashboardStats($recruiter)
    {
        return [
            'total_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->count(),
            'pending_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'pending')->count(),
            'approved_requests' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'approved')->count(),
            'meeting_arranged' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'meeting_arranged')->count(),
            'completed_projects' => TalentRequest::where('recruiter_id', $recruiter->id)->where('status', 'completed')->count(),
            'success_rate' => $this->calculateSuccessRate($recruiter->id),
            'avg_response_time' => $this->calculateAvgResponseTime($recruiter->id),
            'top_skills_requested' => $this->getTopSkillsRequested($recruiter->id),
            'monthly_activity' => $this->getMonthlyActivity($recruiter->id),
            'talent_engagement' => $this->getTalentEngagementStats($recruiter->id)
        ];
    }

    private function calculateSuccessRate($recruiterId)
    {
        $totalRequests = TalentRequest::where('recruiter_id', $recruiterId)->count();
        if ($totalRequests == 0) return 0;

        $successfulRequests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereIn('status', ['approved', 'meeting_arranged', 'completed'])
            ->count();

        return round(($successfulRequests / $totalRequests) * 100, 1);
    }

    private function calculateAvgResponseTime($recruiterId)
    {
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('approved_at')
            ->get();

        if ($requests->count() == 0) return 'N/A';

        $totalHours = 0;
        foreach ($requests as $request) {
            $totalHours += $request->created_at->diffInHours($request->approved_at);
        }

        $avgHours = $totalHours / $requests->count();
        return $avgHours < 24 ? round($avgHours, 1) . 'h' : round($avgHours / 24, 1) . 'd';
    }

    private function getTopSkillsRequested($recruiterId)
    {
        // This would analyze the requirements field to extract common skills
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('requirements')
            ->get();

        $skillCounts = [];
        $commonSkills = ['PHP', 'JavaScript', 'Python', 'React', 'Vue', 'Laravel', 'Node.js', 'MySQL', 'MongoDB'];

        foreach ($requests as $request) {
            foreach ($commonSkills as $skill) {
                if (stripos($request->requirements, $skill) !== false) {
                    $skillCounts[$skill] = ($skillCounts[$skill] ?? 0) + 1;
                }
            }
        }

        arsort($skillCounts);
        return array_slice($skillCounts, 0, 5, true);
    }

    private function getMonthlyActivity($recruiterId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = TalentRequest::where('recruiter_id', $recruiterId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $months[$date->format('M Y')] = $count;
        }
        return $months;
    }

    private function getTalentEngagementStats($recruiterId)
    {
        $totalSent = TalentRequest::where('recruiter_id', $recruiterId)->count();
        $responded = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotIn('status', ['pending'])
            ->count();

        return [
            'total_sent' => $totalSent,
            'responded' => $responded,
            'response_rate' => $totalSent > 0 ? round(($responded / $totalSent) * 100, 1) : 0
        ];
    }

    public function submitTalentRequest(Request $request)
    {
        $request->validate([
            'talent_id' => 'required|string', // Changed to string to accept comma-separated values
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'requirements' => 'nullable|string',
            'budget_range' => 'nullable|string|max:100',
            'project_duration' => 'required|string|max:100',
            'project_id' => 'nullable|exists:projects,id',
            'is_project_assignment' => 'nullable|boolean'
        ]);

        $user = Auth::user();
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return response()->json(['error' => 'Recruiter profile not found'], 404);
        }

        // Parse talent IDs (handle both single ID and comma-separated multiple IDs)
        $talentIdsString = $request->talent_id;
        $talentIds = array_map('trim', explode(',', $talentIdsString));
        $talentIds = array_filter($talentIds); // Remove empty values

        if (empty($talentIds)) {
            return response()->json(['error' => 'No valid talent IDs provided'], 400);
        }

        // Validate that all talent IDs exist
        $existingTalents = \App\Models\Talent::whereIn('id', $talentIds)->get();
        if ($existingTalents->count() !== count($talentIds)) {
            return response()->json(['error' => 'One or more talent IDs are invalid'], 400);
        }

        // Variables to track results
        $successfulRequests = [];
        $failedRequests = [];
        $projectDuration = $request->project_duration;
        $durationInMonths = TalentRequest::parseDurationToMonths($projectDuration);
        $projectStartDate = now()->addDays(7); // Projects start 1 week from request
        $projectEndDate = $projectStartDate->copy()->addMonths($durationInMonths);

        // Process each talent ID
        foreach ($existingTalents as $talent) {
            $talentUserId = $talent->user_id;

            // Check if request already exists for this talent FIRST (before time-blocking check)
            // This ensures specific error messages for duplicate/onboarded requests
            $existingRequest = TalentRequest::where('recruiter_id', $recruiter->id)
                ->where('talent_user_id', $talentUserId)
                ->whereIn('status', ['pending', 'approved', 'meeting_arranged', 'onboarded'])
                ->first();

            if ($existingRequest) {
                // Check if the talent is already onboarded for a different request
                if ($existingRequest->status === 'onboarded') {
                    $failedRequests[] = [
                        'talent_name' => $talent->user->name,
                        'error' => 'talent_already_onboarded',
                        'message' => 'This talent is already onboarded in your organization',
                        'details' => 'The talent is currently onboarded for project "' . $existingRequest->project_title . '".'
                    ];
                    continue; // Skip this talent and move to next
                }

                // Generic message for other active request types
                $failedRequests[] = [
                    'talent_name' => $talent->user->name,
                    'error' => 'active_request_exists',
                    'message' => 'You already have an active request for this talent',
                    'details' => 'Please wait for your current request to be processed or completed before submitting a new one.'
                ];
                continue; // Skip this talent and move to next
            }

            // Check if talent is available for the proposed project duration
            if (!TalentRequest::isTalentAvailable($talentUserId, $projectStartDate, $projectEndDate)) {
                $activeRequests = TalentRequest::getActiveBlockingRequestsForTalent($talentUserId);
                $nextAvailable = $activeRequests->max('project_end_date');

                $failedRequests[] = [
                    'talent_name' => $talent->user->name,
                    'error' => 'talent_not_available',
                    'message' => "This talent is not available for the requested project duration",
                    'details' => "Already committed to other projects until " . $nextAvailable->format('M d, Y'),
                    'next_available_date' => $nextAvailable->copy()->addDay()->format('Y-m-d')
                ];
                continue; // Skip this talent and move to next
            }

            // Create talent request for this talent
            try {
                $talentRequestData = [
                    'recruiter_id' => $recruiter->id,
                    'talent_id' => $talent->id, // Single talent ID
                    'talent_user_id' => $talentUserId,
                    'project_title' => $request->project_title,
                    'project_description' => $request->project_description,
                    'requirements' => $request->requirements,
                    'budget_range' => $request->budget_range,
                    'project_duration' => $request->project_duration,
                    'status' => 'pending',
                    'project_start_date' => $projectStartDate,
                    'project_end_date' => $projectEndDate,
                    'is_blocking_talent' => true,
                    'blocking_notes' => "Project duration: {$projectDuration}, estimated from {$projectStartDate->format('M d, Y')} to {$projectEndDate->format('M d, Y')}"
                ];

                // Add project-specific fields if this is a project assignment
                if ($request->project_id) {
                    $talentRequestData['project_id'] = $request->project_id;
                }

                $talentRequest = TalentRequest::create($talentRequestData);

                // Send notifications to both talent and admin
                $notificationsSent = $this->notificationService->notifyNewTalentRequest($talentRequest);

                $successfulRequests[] = [
                    'talent_name' => $talent->user->name,
                    'request_id' => $talentRequest->id,
                    'notifications_sent' => $notificationsSent
                ];

            } catch (\Exception $e) {
                $failedRequests[] = [
                    'talent_name' => $talent->user->name,
                    'error' => 'creation_failed',
                    'message' => 'Failed to create talent request',
                    'details' => $e->getMessage()
                ];
            }
        }

        // Prepare response based on results
        $totalRequested = count($talentIds);
        $successCount = count($successfulRequests);
        $failedCount = count($failedRequests);

        if ($successCount > 0 && $failedCount === 0) {
            // All requests successful
            $talentNames = array_column($successfulRequests, 'talent_name');
            return response()->json([
                'success' => true,
                'message' => $request->is_project_assignment ?
                    "Project talent assignment requests submitted successfully for {$successCount} talent(s): " . implode(', ', $talentNames) :
                    "Talent requests submitted successfully for {$successCount} talent(s): " . implode(', ', $talentNames),
                'successful_requests' => $successfulRequests,
                'project_timeline' => [
                    'start_date' => $projectStartDate->format('M d, Y'),
                    'end_date' => $projectEndDate->format('M d, Y'),
                    'duration' => $projectDuration
                ]
            ]);
        } elseif ($successCount > 0 && $failedCount > 0) {
            // Partial success
            $successNames = array_column($successfulRequests, 'talent_name');
            return response()->json([
                'success' => true,
                'partial' => true,
                'message' => "Requests submitted for {$successCount} out of {$totalRequested} talents: " . implode(', ', $successNames),
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'project_timeline' => [
                    'start_date' => $projectStartDate->format('M d, Y'),
                    'end_date' => $projectEndDate->format('M d, Y'),
                    'duration' => $projectDuration
                ]
            ]);
        } else {
            // All requests failed
            return response()->json([
                'success' => false,
                'message' => "Unable to submit talent requests for any of the selected talents",
                'failed_requests' => $failedRequests
            ], 400);
        }
    }

    public function myRequests()
    {
        $userId = Auth::id();
        $user = User::with('recruiter')->find($userId);
        $title = 'My Talent Requests';
        $roles = 'Recruiter';
        $assignedKelas = [];
        $recruiter = $user->recruiter;

        $requests = collect();
        if ($recruiter) {
            $requests = TalentRequest::with(['talent.user'])
                ->where('recruiter_id', $recruiter->id)
                ->latest()
                ->paginate(10);
        }

        return view('admin.recruiter.requests', compact('user', 'title', 'roles', 'assignedKelas', 'requests'));
    }

    public function getScoutingReport(Request $request, $talentId)
    {
        try {
            $talent = Talent::with(['user'])->findOrFail($talentId);
            $metrics = $this->scoutingService->getTalentScoutingMetrics($talent);

            return response()->json([
                'success' => true,
                'talent' => [
                    'id' => $talent->id,
                    'name' => $talent->user->name,
                    'email' => $talent->user->email,
                    'profession' => $talent->user->pekerjaan,
                ],
                'metrics' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load scouting report'
            ], 500);
        }
    }

    public function requestDetails($requestId)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recruiter profile not found'
                ], 404);
            }

            $request = TalentRequest::with(['talent.user'])
                ->where('id', $requestId)
                ->where('recruiter_id', $recruiter->id)
                ->first();

            if (!$request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or access denied'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'request' => [
                    'id' => $request->id,
                    'talent_name' => $request->talent->user->name,
                    'talent_email' => $request->talent->user->email,
                    'talent_position' => $request->talent->user->pekerjaan,
                    'project_title' => $request->project_title,
                    'project_description' => $request->project_description,
                    'requirements' => $request->requirements,
                    'budget' => $request->budget_range,
                    'project_duration' => $request->project_duration,
                    'status' => ucfirst($request->status),
                    'created_at' => $request->created_at->format('M d, Y h:i A'),
                    'updated_at' => $request->updated_at->format('M d, Y h:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Request details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load request details'
            ], 500);
        }
    }

    /**
     * Get real-time analytics data for dashboard widgets
     */
    public function getAnalyticsData(Request $request)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            $timeframe = $request->get('timeframe', '30'); // days
            $startDate = now()->subDays($timeframe);

            $data = [
                'request_trends' => $this->getRequestTrends($recruiter->id, $startDate),
                'skill_demand' => $this->getSkillDemandAnalysis($recruiter->id),
                'market_insights' => $this->getMarketInsights(),
                'performance_metrics' => $this->getPerformanceMetrics($recruiter->id, $startDate),
                'talent_pool_stats' => $this->getTalentPoolStats()
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Analytics data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load analytics data'], 500);
        }
    }

    private function getRequestTrends($recruiterId, $startDate)
    {
        $trends = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trends->mapWithKeys(function ($item) {
            return [$item->date => $item->count];
        });
    }

    private function getSkillDemandAnalysis($recruiterId)
    {
        // Leverage existing analytics service
        $analytics = $this->analyticsService->getSkillAnalytics();
        return $analytics['market_demand_analysis'] ?? [];
    }

    private function getMarketInsights()
    {
        $totalTalents = Talent::where('is_active', true)->count();
        $availableForScouting = User::where('available_for_scouting', true)->count();
        $recentlyActive = User::where('available_for_scouting', true)
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            'total_talent_pool' => $totalTalents,
            'available_talents' => $availableForScouting,
            'recently_active' => $recentlyActive,
            'market_growth' => $this->calculateMarketGrowth()
        ];
    }

    private function calculateMarketGrowth()
    {
        $currentMonth = User::where('available_for_scouting', true)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonth = User::where('available_for_scouting', true)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        if ($lastMonth == 0) return 0;
        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function getPerformanceMetrics($recruiterId, $startDate)
    {
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate);

        return [
            'total_requests' => $requests->count(),
            'approval_rate' => $this->calculateApprovalRate($recruiterId, $startDate),
            'avg_project_value' => $this->calculateAvgProjectValue($recruiterId),
            'repeat_collaborations' => $this->getRepeatCollaborations($recruiterId)
        ];
    }

    private function calculateApprovalRate($recruiterId, $startDate)
    {
        $total = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->count();

        if ($total == 0) return 0;

        $approved = TalentRequest::where('recruiter_id', $recruiterId)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['approved', 'meeting_arranged', 'completed'])
            ->count();

        return round(($approved / $total) * 100, 1);
    }

    private function calculateAvgProjectValue($recruiterId)
    {
        // This would need budget range parsing or additional budget fields
        return 'N/A'; // Placeholder for now
    }

    private function getRepeatCollaborations($recruiterId)
    {
        $talentCounts = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereIn('status', ['completed'])
            ->groupBy('talent_id')
            ->selectRaw('talent_id, COUNT(*) as count')
            ->having('count', '>', 1)
            ->count();

        return $talentCounts;
    }

    private function getTalentPoolStats()
    {
        $skills = $this->analyticsService->getSkillAnalytics();

        return [
            'total_skills' => count($skills['skill_categories'] ?? []),
            'high_demand_skills' => count(array_filter($skills['market_demand_analysis'] ?? [],
                function($demand) { return $demand > 50; })),
            'emerging_skills' => $this->getEmergingSkills(),
            'skill_trends' => $skills['skill_progression_trends'] ?? []
        ];
    }

    private function getEmergingSkills()
    {
        // Skills that have shown growth in the last 3 months
        return ['React Native', 'Flutter', 'Machine Learning', 'DevOps', 'Cloud Architecture'];
    }

    /**
     * Get talent recommendations based on recent requests
     */
    public function getTalentRecommendations(Request $request)
    {
        try {
            $user = Auth::user();
            $recruiter = $user->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            // Get top talents with enhanced scoring
            $recommendations = $this->scoutingService->getTopTalents(12);

            // Add personalized scoring based on recruiter's history
            $recommendations = $recommendations->map(function($talent) use ($recruiter) {
                $talent->personalized_score = $this->calculatePersonalizedScore($talent, $recruiter);
                return $talent;
            });

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            Log::error('Talent recommendations error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load recommendations'], 500);
        }
    }

    private function calculatePersonalizedScore($talent, $recruiter)
    {
        $score = 50; // Base score

        // Previous interactions bonus
        $previousRequests = TalentRequest::where('recruiter_id', $recruiter->id)
            ->where('talent_id', $talent->id)
            ->whereIn('status', ['approved', 'completed'])
            ->count();
        $score += $previousRequests * 10;

        // Skill match bonus based on recruiter's request history
        $recruiterSkillPreferences = $this->getRecruiterSkillPreferences($recruiter->id);
        $talentSkills = $talent->user ? $talent->user->getTalentSkillsArray() : [];

        $skillMatches = array_intersect($recruiterSkillPreferences,
            array_column($talentSkills, 'name'));
        $score += count($skillMatches) * 5;

        // Availability bonus
        if ($talent->user->available_for_scouting) {
            $score += 10;
        }

        return min($score, 100); // Cap at 100
    }

    private function getRecruiterSkillPreferences($recruiterId)
    {
        // Extract common skills from recruiter's past requests
        $requests = TalentRequest::where('recruiter_id', $recruiterId)
            ->whereNotNull('requirements')
            ->get();

        $skills = [];
        foreach ($requests as $request) {
            // Simple extraction - in reality you'd use NLP or better parsing
            preg_match_all('/\b(PHP|JavaScript|Python|React|Vue|Laravel|Node\.js|MySQL|MongoDB|CSS|HTML)\b/i',
                $request->requirements, $matches);
            $skills = array_merge($skills, $matches[0]);
        }

        return array_unique(array_map('strtolower', $skills));
    }

    public function exportRequestsToPdf()
    {
        $userId = Auth::id();
        $user = User::with('recruiter')->find($userId);
        $recruiter = $user->recruiter;

        if (!$recruiter) {
            return response()->json(['error' => 'Recruiter profile not found'], 404);
        }

        $requests = TalentRequest::with(['talent.user'])
            ->where('recruiter_id', $recruiter->id)
            ->latest()
            ->get();

        $pdf = SnappyPdf::loadView('admin.recruiter.requests_pdf', compact('user', 'requests'));
        return $pdf->download('talent_requests.pdf');
    }

    /**
     * Export recruiter's talent request history as PDF
     */
    public function exportRequestHistory()
    {
        try {
            $recruiter = Auth::user()->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            // Get all talent requests for this recruiter
            $requests = TalentRequest::with(['talent.user', 'recruiter.user'])
                ->where('recruiter_id', $recruiter->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Attach standardized talentSkills array to each request for safe Blade usage
            $requests->transform(function ($request) {
                // Prefer direct user reference if available, else fallback to talent->user
                $talentUser = $request->talentUser ?? ($request->talent ? $request->talent->user : null);
                $request->talentSkills = $talentUser ? $talentUser->getTalentSkillsArray() : [];
                return $request;
            });

            $data = [
                'recruiter' => $recruiter,
                'recruiter_user' => Auth::user(),
                'requests' => $requests,
                'total_requests' => $requests->count(),
                'pending_requests' => $requests->where('status', 'pending')->count(),
                'approved_requests' => $requests->where('status', 'approved')->count(),
                'completed_requests' => $requests->where('status', 'completed')->count(),
                'export_date' => now()->format('d M Y H:i'),
            ];

            $filename = 'talent-request-history-' . now()->format('Y-m-d') . '.pdf';

            // Use DomPDF directly since wkhtmltopdf is not installed
            $pdf = Pdf::loadView('exports.recruiter.request-history', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => 'Please contact support if this issue persists.',
                'debug' => app()->isLocal() ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Export onboarded talent information as PDF
     */
    public function exportOnboardedTalents()
    {
        try {
            $recruiter = Auth::user()->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

            // Get successfully onboarded talents (both admin and talent accepted)
            $onboardedRequests = TalentRequest::with(['talent.user', 'recruiter.user'])
                ->where('recruiter_id', $recruiter->id)
                ->whereIn('status', ['onboarded', 'completed'])
                ->where('both_parties_accepted', true)
                ->orderBy('created_at', 'desc')
                ->get();

            $data = [
                'recruiter' => $recruiter,
                'recruiter_user' => Auth::user(),
                'onboarded_requests' => $onboardedRequests,
                'total_onboarded' => $onboardedRequests->count(),
                'export_date' => now()->format('d M Y H:i'),
            ];

            $filename = 'onboarded-talents-' . now()->format('Y-m-d') . '.pdf';

            // Use DomPDF directly since wkhtmltopdf is not installed
            $pdf = Pdf::loadView('exports.recruiter.onboarded-talents', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => 'Please contact support if this issue persists.',
                'debug' => app()->isLocal() ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get redflag history for a specific talent
     */
    public function getTalentRedflagHistory($talentId)
    {
        try {
            $recruiter = Auth::user()->recruiter;

            if (!$recruiter) {
                return response()->json(['error' => 'Recruiter profile not found'], 404);
            }

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
                    'redflagged_at' => $request->redflagged_at ? $request->redflagged_at->format('M d, Y') : null,
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
     * Get talent details for recruiter view
     */
    public function getTalentDetails(Talent $talent)
    {
        try {
            // Eager load necessary relationships
            $talent->load(['user']);

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
                'joined_date' => $talent->created_at->format('M d, Y'),
                'skills' => $talent->user->getTalentSkillsArray() ?? [],
                'portfolio' => [], // Can be extended later if needed
            ];

            return response()->json([
                'success' => true,
                'talent' => $talentData
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching talent details for recruiter - talent ID {$talent->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving talent details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get talent details by user ID for recruiter view
     */
    public function getTalentDetailsByUserId(User $user)
    {
        try {
            $talent = $user->talent;
            if (!$talent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Talent profile not found for this user'
                ], 404);
            }

            // Use the existing getTalentDetails logic
            return $this->getTalentDetails($talent);
        } catch (\Exception $e) {
            Log::error("Error fetching talent details by user ID for recruiter - user ID {$user->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving talent details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
