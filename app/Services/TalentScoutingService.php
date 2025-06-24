<?php

namespace App\Services;

use App\Models\User;
use App\Models\Talent;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\CourseTrainee;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TalentScoutingService
{
    /**
     * Get comprehensive scouting metrics for a talent
     */
    public function getTalentScoutingMetrics(Talent $talent): array
    {
        $user = $talent->user;

        $metrics = [
            'learning_velocity' => $this->calculateLearningVelocity($user),
            'consistency' => $this->calculateConsistency($user),
            'adaptability' => $this->calculateAdaptability($user),
            'progress_tracking' => $this->calculateProgressTracking($user),
            'certifications' => $this->getCertificationMetrics($user),
            'quiz_performance' => $this->getQuizPerformanceMetrics($user),
            'completion_rate' => $this->calculateCompletionRate($user),
            'market_demand' => $this->calculateMarketDemandScore($user),
            'activity_level' => $this->calculateActivityLevel($user),
            'challenge_seeking' => $this->calculateChallengeSeeking($user),
            'breadth_vs_depth' => $this->calculateBreadthVsDepth($user),
            'profile_completeness' => $this->calculateProfileCompleteness($user),
            'overall_score' => 0 // Will be calculated below
        ];

        // Calculate overall score based on weighted metrics
        $metrics['overall_score'] = $this->calculateOverallScore($metrics);

        return $metrics;
    }

    /**
     * Calculate learning velocity (courses per month, time per module)
     */
    private function calculateLearningVelocity(User $user): array
    {
        // Get completed courses (progress = 100)
        $progress = CourseProgress::where('user_id', $user->id)
            ->where('progress', 100)
            ->get();

        $monthsSinceStart = max(1, Carbon::parse($user->created_at)->diffInMonths(now()));
        $coursesPerMonth = $progress->count() / $monthsSinceStart;

        // Calculate average time per course completion
        $averageCompletionTime = 0;
        if ($progress->count() > 0) {
            $totalDays = $progress->sum(function($p) {
                return Carbon::parse($p->created_at)->diffInDays($p->updated_at);
            });
            $averageCompletionTime = $totalDays / $progress->count();
        }

        // Score from 0-100
        $velocityScore = min(100, ($coursesPerMonth * 10) + (max(0, 30 - $averageCompletionTime) * 2));

        return [
            'courses_per_month' => round($coursesPerMonth, 2),
            'average_completion_days' => round($averageCompletionTime, 1),
            'score' => round($velocityScore, 1),
            'rating' => $this->getStarRating($velocityScore)
        ];
    }

    /**
     * Calculate consistency (regular learning patterns)
     */
    private function calculateConsistency(User $user): array
    {
        $recentActivity = CourseProgress::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as activities')
            ->groupBy('date')
            ->get();

        $activeDays = $recentActivity->count();
        $totalDays = 30;
        $consistencyPercentage = ($activeDays / $totalDays) * 100;

        // Calculate streak
        $streak = $this->calculateLearningStreak($user);

        $consistencyScore = ($consistencyPercentage * 0.7) + ($streak * 2);

        return [
            'active_days_last_30' => $activeDays,
            'consistency_percentage' => round($consistencyPercentage, 1),
            'current_streak' => $streak,
            'score' => round(min(100, $consistencyScore), 1),
            'rating' => $this->getStarRating($consistencyScore)
        ];
    }

    /**
     * Calculate adaptability across different course types
     */
    private function calculateAdaptability(User $user): array
    {
        // Get completed courses (progress = 100)
        $courses = CourseProgress::with('course.category')
            ->where('user_id', $user->id)
            ->where('progress', 100)
            ->get();

        $categories = $courses->pluck('course.category.name')->unique()->filter()->count();
        $avgQuizScore = QuizAttempt::where('user_id', $user->id)
            ->avg('score') ?? 0;

        // Score based on category diversity and quiz performance
        $adaptabilityScore = ($categories * 15) + ($avgQuizScore * 0.5);

        return [
            'course_categories' => $categories,
            'average_quiz_score' => round($avgQuizScore, 1),
            'score' => round(min(100, $adaptabilityScore), 1),
            'rating' => $this->getStarRating($adaptabilityScore)
        ];
    }

    /**
     * Calculate progress tracking metrics
     */
    private function calculateProgressTracking(User $user): array
    {
        // Total enrolled courses from CourseTrainee
        $totalCourses = CourseTrainee::where('user_id', $user->id)->count();

        // Completed courses (progress = 100)
        $completedCourses = CourseProgress::where('user_id', $user->id)
            ->where('progress', 100)->count();

        // In progress courses (progress > 0 but < 100)
        $inProgressCourses = CourseProgress::where('user_id', $user->id)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)->count();

        $completionRate = $totalCourses > 0 ? ($completedCourses / $totalCourses) * 100 : 0;
        $progressScore = $completionRate;

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'completion_rate' => round($completionRate, 1),
            'score' => round($progressScore, 1),
            'rating' => $this->getStarRating($progressScore)
        ];
    }

    /**
     * Get certification metrics
     */
    private function getCertificationMetrics(User $user): array
    {
        $certificates = Certificate::where('user_id', $user->id)->get();
        $recentCertificates = $certificates->where('created_at', '>=', now()->subMonths(6))->count();

        $certificationScore = ($certificates->count() * 10) + ($recentCertificates * 5);

        return [
            'total_certificates' => $certificates->count(),
            'recent_certificates' => $recentCertificates,
            'score' => round(min(100, $certificationScore), 1),
            'rating' => $this->getStarRating($certificationScore)
        ];
    }

    /**
     * Get quiz performance metrics
     */
    private function getQuizPerformanceMetrics(User $user): array
    {
        $quizAttempts = QuizAttempt::where('user_id', $user->id)->get();
        $averageScore = $quizAttempts->avg('score') ?? 0;
        $totalAttempts = $quizAttempts->count();
        $highScores = $quizAttempts->where('score', '>=', 80)->count();

        $performanceScore = ($averageScore * 0.7) + (($highScores / max(1, $totalAttempts)) * 30);

        return [
            'average_score' => round($averageScore, 1),
            'total_attempts' => $totalAttempts,
            'high_scores_count' => $highScores,
            'high_score_percentage' => $totalAttempts > 0 ? round(($highScores / $totalAttempts) * 100, 1) : 0,
            'score' => round($performanceScore, 1),
            'rating' => $this->getStarRating($performanceScore)
        ];
    }

    /**
     * Calculate overall completion rate
     */
    private function calculateCompletionRate(User $user): array
    {
        $metrics = $this->calculateProgressTracking($user);
        return [
            'rate' => $metrics['completion_rate'],
            'score' => $metrics['score'],
            'rating' => $metrics['rating']
        ];
    }

    /**
     * Calculate market demand score based on completed courses
     */
    private function calculateMarketDemandScore(User $user): array
    {
        // Get completed courses and their categories (progress = 100)
        $completedCourses = CourseProgress::with('course.category')
            ->where('user_id', $user->id)
            ->where('progress', 100)
            ->get();

        // High demand categories (can be configured)
        $highDemandCategories = [
            'Web Development', 'Mobile Development', 'Data Science',
            'Machine Learning', 'Cloud Computing', 'Cybersecurity',
            'DevOps', 'AI/ML', 'Blockchain'
        ];

        $highDemandCourses = $completedCourses->filter(function($progress) use ($highDemandCategories) {
            return in_array($progress->course->category->name ?? '', $highDemandCategories);
        })->count();

        $demandScore = ($highDemandCourses / max(1, $completedCourses->count())) * 100;

        return [
            'high_demand_courses' => $highDemandCourses,
            'total_courses' => $completedCourses->count(),
            'demand_percentage' => round($demandScore, 1),
            'score' => round($demandScore, 1),
            'rating' => $this->getStarRating($demandScore)
        ];
    }

    /**
     * Calculate activity level
     */
    private function calculateActivityLevel(User $user): array
    {
        $recentActivity = CourseProgress::where('user_id', $user->id)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        $dailyAverage = $recentActivity / 30;
        $activityScore = min(100, $dailyAverage * 20);

        return [
            'recent_activities' => $recentActivity,
            'daily_average' => round($dailyAverage, 2),
            'score' => round($activityScore, 1),
            'rating' => $this->getStarRating($activityScore)
        ];
    }

    /**
     * Calculate challenge seeking behavior
     */
    private function calculateChallengeSeeking(User $user): array
    {
        // Look for advanced/difficult courses (courses with lower completion rates)
        // Get user's completed courses (progress = 100)
        $userCourses = CourseProgress::with('course')
            ->where('user_id', $user->id)
            ->where('progress', 100)
            ->get();

        $challengingCourses = 0;
        foreach ($userCourses as $progress) {
            // Calculate course completion rate across all users
            $totalEnrolled = CourseTrainee::where('course_id', $progress->course_id)->count();
            $totalCompleted = CourseProgress::where('course_id', $progress->course_id)
                ->where('progress', 100)->count();

            $courseCompletionRate = $totalEnrolled > 0 ? $totalCompleted / $totalEnrolled : 0;

            if ($courseCompletionRate < 0.5) { // Less than 50% completion rate = challenging
                $challengingCourses++;
            }
        }

        $challengeScore = ($challengingCourses / max(1, $userCourses->count())) * 100;

        return [
            'challenging_courses' => $challengingCourses,
            'total_courses' => $userCourses->count(),
            'challenge_percentage' => round($challengeScore, 1),
            'score' => round($challengeScore, 1),
            'rating' => $this->getStarRating($challengeScore)
        ];
    }

    /**
     * Calculate breadth vs depth
     */
    private function calculateBreadthVsDepth(User $user): array
    {
        // Get completed courses (progress = 100) with categories
        $courses = CourseProgress::with('course.category')
            ->where('user_id', $user->id)
            ->where('progress', 100)
            ->get();

        $categories = $courses->pluck('course.category.name')->filter()->countBy();
        $totalCourses = $courses->count();
        $uniqueCategories = $categories->count();

        $breadthScore = $uniqueCategories * 10; // More categories = higher breadth
        $depthScore = $totalCourses > 0 ? ($categories->max() / $totalCourses) * 100 : 0; // Concentration in one category

        $balanceScore = min(100, ($breadthScore + $depthScore) / 2);

        return [
            'unique_categories' => $uniqueCategories,
            'total_courses' => $totalCourses,
            'breadth_score' => round($breadthScore, 1),
            'depth_score' => round($depthScore, 1),
            'balance_score' => round($balanceScore, 1),
            'score' => round($balanceScore, 1),
            'rating' => $this->getStarRating($balanceScore)
        ];
    }

    /**
     * Calculate profile completeness
     */
    private function calculateProfileCompleteness(User $user): array
    {
        $fields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'phone' => !empty($user->no_telp),
            'address' => !empty($user->alamat),
            'job' => !empty($user->pekerjaan),
            'avatar' => !empty($user->avatar)
        ];

        $completedFields = array_sum($fields);
        $totalFields = count($fields);
        $completenessScore = ($completedFields / $totalFields) * 100;

        return [
            'completed_fields' => $completedFields,
            'total_fields' => $totalFields,
            'completion_percentage' => round($completenessScore, 1),
            'missing_fields' => array_keys(array_filter($fields, fn($v) => !$v)),
            'score' => round($completenessScore, 1),
            'rating' => $this->getStarRating($completenessScore)
        ];
    }

    /**
     * Calculate learning streak
     */
    private function calculateLearningStreak(User $user): int
    {
        $activities = CourseProgress::where('user_id', $user->id)
            ->selectRaw('DATE(updated_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        $streak = 0;
        $currentDate = now()->format('Y-m-d');

        foreach ($activities as $date) {
            if ($date === $currentDate || $date === Carbon::parse($currentDate)->subDays($streak + 1)->format('Y-m-d')) {
                $streak++;
                $currentDate = Carbon::parse($currentDate)->subDay()->format('Y-m-d');
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Convert score to star rating (1-5 stars)
     */
    private function getStarRating(float $score): int
    {
        if ($score >= 90) return 5;
        if ($score >= 75) return 4;
        if ($score >= 60) return 3;
        if ($score >= 40) return 2;
        return 1;
    }

    /**
     * Invalidate cached talent metrics
     */
    public function invalidateTalentMetricsCache(Talent $talent): void
    {
        $cacheKey = "talent_metrics_{$talent->id}";
        cache()->forget($cacheKey);
    }

    /**
     * Refresh talent metrics cache
     */
    public function refreshTalentMetricsCache(Talent $talent): array
    {
        // First invalidate existing cache
        $this->invalidateTalentMetricsCache($talent);

        // Calculate fresh metrics
        $metrics = $this->getTalentScoutingMetrics($talent);

        // Cache the new metrics
        $cacheKey = "talent_metrics_{$talent->id}";
        cache()->put($cacheKey, $metrics, now()->addHours(24));

        return $metrics;
    }

    /**
     * Get talents with scouting filters
     */
    public function getFilteredTalents(array $filters = [])
    {
        $query = Talent::with(['user', 'talentRequests'])
            ->where('is_active', true)
            ->whereHas('user', function($q) {
                $q->whereNotNull('name')->whereNotNull('email');
            });

        // Apply filters here based on scouting metrics
        // This would be expanded based on the specific filter requirements

        return $query->latest()->paginate(12);
    }

    /**
     * Get top talents based on overall scouting score
     */
    public function getTopTalents(int $limit = 10): Collection
    {
        $talents = Talent::with(['user'])->where('is_active', true)->get();

        $scoredTalents = $talents->map(function($talent) {
            $metrics = $this->getTalentScoutingMetrics($talent);
            $overallScore = $this->calculateOverallScore($metrics);
            $talent->scouting_score = $overallScore;
            return $talent;
        })->sortByDesc('scouting_score')->take($limit);

        return $scoredTalents;
    }

    /**
     * Calculate overall scouting score
     */
    private function calculateOverallScore(array $metrics): float
    {
        $weights = [
            'learning_velocity' => 0.15,
            'consistency' => 0.15,
            'adaptability' => 0.10,
            'progress_tracking' => 0.10,
            'certifications' => 0.15,
            'quiz_performance' => 0.15,
            'market_demand' => 0.10,
            'activity_level' => 0.05,
            'profile_completeness' => 0.05
        ];

        $totalScore = 0;
        foreach ($weights as $metric => $weight) {
            if (isset($metrics[$metric]['score'])) {
                $totalScore += $metrics[$metric]['score'] * $weight;
            }
        }

        return round($totalScore, 1);
    }

    /**
     * Get basic talent statistics for dashboard display
     */
    public function getBasicTalentStats(Talent $talent): array
    {
        $user = $talent->user;

        // Get completed courses count
        $completedCourses = CourseProgress::where('user_id', $user->id)
            ->where('progress', 100)->count();

        // Get certificates count
        $certificates = Certificate::where('user_id', $user->id)->count();

        // Get quiz average
        $quizAverage = QuizAttempt::where('user_id', $user->id)->avg('score') ?? 0;

        return [
            'completed_courses' => $completedCourses,
            'certificates' => $certificates,
            'quiz_average' => round($quizAverage, 1)
        ];
    }
}
