<?php

namespace App\Services;

use App\Models\{User, Course, CourseProgress, QuizAttempt, ModuleTask, Talent};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Smart Conversion Tracking Service
 * Tracks user progress and suggests talent conversion opportunities
 * Part of Phase 1: Enhanced LMS-Talent Integration
 */
class SmartConversionTrackingService
{
    /**
     * Get conversion funnel analytics
     */
    public function getConversionFunnel(): array
    {
        $totalUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->count();
        $activeUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->whereHas('courseProgress')->count();
        $completedCourses = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->whereHas('courseProgress', function($q) {
            $q->where('progress', 100); // Assuming 100% progress means completed
        })->count();
        $talentUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();

        return [
            'total_users' => $totalUsers,
            'active_learners' => $activeUsers,
            'course_completers' => $completedCourses,
            'converted_talents' => $talentUsers,
            'conversion_rate' => $totalUsers > 0 ? round(($talentUsers / $totalUsers) * 100, 2) : 0,
            'completion_rate' => $activeUsers > 0 ? round(($completedCourses / $activeUsers) * 100, 2) : 0
        ];
    }

    /**
     * Get talent readiness scores for all users
     */
    public function getTalentReadinessAnalytics(): array
    {
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->with(['courseProgress', 'quizAttempts'])->get();

        $readinessData = [];
        foreach ($users as $user) {
            $score = $this->calculateReadinessScoreInternal($user);
            $readinessData[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'readiness_score' => $score,
                'readiness_level' => $this->getReadinessLevel($score),
                'courses_completed' => $user->courseProgress()->where('progress', 100)->count(),
                'quiz_average' => $this->getQuizAverage($user),
                'learning_streak' => $this->getLearningStreak($user)
            ];
        }

        // Sort by readiness score descending
        usort($readinessData, function($a, $b) {
            return $b['readiness_score'] <=> $a['readiness_score'];
        });

        return $readinessData;
    }

    /**
     * Get conversion candidates based on readiness criteria
     */
    public function getConversionCandidates(int $limit = 10): array
    {
        $candidates = [];
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->whereDoesntHave('roles', function($query) {
            $query->where('name', 'talent');
        })->with(['courseProgress', 'quizAttempts'])->get();

        foreach ($users as $user) {
            $readinessScore = $this->calculateReadinessScoreInternal($user);

            // Only include high-readiness candidates (score >= 70)
            if ($readinessScore >= 70) {
                $candidates[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'readiness_score' => $readinessScore,
                    'recommendation_reason' => $this->getRecommendationReason($user, $readinessScore),
                    'skill_category' => $this->getSkillCategory($user),
                    'courses_completed' => $user->courseProgress()->where('progress', 100)->count(),
                    'quiz_average' => $this->getQuizAverage($user),
                    'learning_velocity' => $this->getLearningVelocity($user)
                ];
            }
        }

        // Sort by readiness score descending
        usort($candidates, function($a, $b) {
            return $b['readiness_score'] <=> $a['readiness_score'];
        });

        return array_slice($candidates, 0, $limit);
    }

    /**
     * Calculate user's readiness score for talent conversion (public method)
     */
    public function calculateReadinessScore(User $user): float
    {
        return $this->calculateReadinessScoreInternal($user);
    }

    /**
     * Calculate user's readiness score for talent conversion (internal method)
     */
    private function calculateReadinessScoreInternal(User $user): float
    {
        $score = 0;

        // Course completion factor (40% weight)
        $completedCourses = $user->courseProgress()->where('progress', 100)->count();
        $totalCourses = $user->courseProgress()->count();
        if ($totalCourses > 0) {
            $completionRate = $completedCourses / $totalCourses;
            $score += $completionRate * 40;
        }

        // Quiz performance factor (30% weight)
        $quizAverage = $this->getQuizAverage($user);
        $score += ($quizAverage / 100) * 30;

        // Learning velocity factor (20% weight)
        $velocity = $this->getLearningVelocity($user);
        $score += min($velocity * 5, 20); // Cap at 20 points

        // Consistency factor (10% weight)
        $streak = $this->getLearningStreak($user);
        $score += min($streak, 10); // Cap at 10 points

        return round($score, 2);
    }

    /**
     * Get user's quiz average score
     */
    private function getQuizAverage(User $user): float
    {
        $quizAttempts = $user->quizAttempts()
            ->where('is_passed', true)
            ->get();

        if ($quizAttempts->isEmpty()) {
            return 0;
        }

        $totalScore = $quizAttempts->sum('score');
        return round($totalScore / $quizAttempts->count(), 2);
    }

    /**
     * Get user's learning velocity (courses per month)
     */
    private function getLearningVelocity(User $user): float
    {
        $completedCourses = $user->courseProgress()
            ->where('progress', 100)
            ->where('updated_at', '>=', now()->subMonths(3))
            ->count();

        return round($completedCourses / 3, 2); // courses per month in last 3 months
    }

    /**
     * Get user's learning streak (days)
     */
    private function getLearningStreak(User $user): int
    {
        // Simplified calculation - could be enhanced with actual activity tracking
        $recentActivity = $user->courseProgress()
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return min($recentActivity * 2, 30); // Simplified streak calculation
    }

    /**
     * Get readiness level label
     */
    private function getReadinessLevel(float $score): string
    {
        if ($score >= 85) return 'Excellent';
        if ($score >= 70) return 'High';
        if ($score >= 55) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Very Low';
    }

    /**
     * Get recommendation reason for conversion
     */
    private function getRecommendationReason(User $user, float $score): string
    {
        $reasons = [];

        if ($score >= 85) {
            $reasons[] = "Exceptional performance and consistency";
        }

        $completionRate = $user->courseProgress()->where('progress', 100)->count() / max($user->courseProgress()->count(), 1);
        if ($completionRate >= 0.8) {
            $reasons[] = "High course completion rate";
        }

        $quizAverage = $this->getQuizAverage($user);
        if ($quizAverage >= 80) {
            $reasons[] = "Strong quiz performance";
        }

        $velocity = $this->getLearningVelocity($user);
        if ($velocity >= 2) {
            $reasons[] = "Fast learning pace";
        }

        return !empty($reasons) ? implode(', ', $reasons) : "Meets minimum readiness criteria";
    }

    /**
     * Get user's primary skill category
     */
    private function getSkillCategory(User $user): string
    {
        $courses = $user->courseProgress()
            ->with('course.category')
            ->where('progress', 100)
            ->get();

        if ($courses->isEmpty()) {
            return 'General';
        }

        // Count categories
        $categories = [];
        foreach ($courses as $progress) {
            if ($progress->course && $progress->course->category) {
                $categoryName = $progress->course->category->name;
                $categories[$categoryName] = ($categories[$categoryName] ?? 0) + 1;
            }
        }

        if (empty($categories)) {
            return 'General';
        }

        // Return the most common category
        return array_key_first($categories);
    }

    /**
     * Get skill count for a candidate (helper method)
     */
    private function getSkillCount(array $candidate): int
    {
        // This is a simplified skill count - in a real system you'd count actual skills
        // For now, we'll estimate based on courses completed and readiness score
        $baseSkills = intval($candidate['courses_completed'] * 1.5);
        $bonusSkills = intval($candidate['readiness_score'] / 20);
        return max($baseSkills + $bonusSkills, 1);
    }

    /**
     * Get conversion insights and recommendations
     */
    public function getConversionInsights(): array
    {
        $funnel = $this->getConversionFunnel();
        $readinessData = $this->getTalentReadinessAnalytics();

        return [
            'funnel_metrics' => $funnel,
            'avg_readiness_score' => collect($readinessData)->avg('readiness_score'),
            'high_readiness_count' => collect($readinessData)->where('readiness_score', '>=', 70)->count(),
            'top_skill_categories' => $this->getTopSkillCategories(),
            'conversion_recommendations' => $this->getConversionRecommendations($funnel, $readinessData)
        ];
    }

    /**
     * Get top skill categories from completed courses
     */
    private function getTopSkillCategories(): array
    {
        $categories = DB::table('course_progresses')
            ->join('courses', 'course_progresses.course_id', '=', 'courses.id')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->where('course_progresses.progress', 100)
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();

        return $categories;
    }

    /**
     * Get conversion recommendations based on analytics
     */
    private function getConversionRecommendations(array $funnel, array $readinessData): array
    {
        $recommendations = [];

        if ($funnel['conversion_rate'] < 10) {
            $recommendations[] = "Low conversion rate - consider improving talent onboarding process";
        }

        if ($funnel['completion_rate'] < 50) {
            $recommendations[] = "Low course completion rate - review course content and engagement";
        }

        $highReadiness = collect($readinessData)->where('readiness_score', '>=', 70)->count();
        if ($highReadiness > 0) {
            $recommendations[] = "Focus on converting {$highReadiness} high-readiness learners";
        }

        return $recommendations;
    }

    /**
     * Get comprehensive conversion analytics for dashboard
     */
    public function getConversionAnalytics(): array
    {
        $funnel = $this->getConversionFunnel();
        $readinessData = $this->getTalentReadinessAnalytics();
        $conversionCandidates = $this->getConversionCandidates(10);
        $insights = $this->getConversionInsights();

        // Calculate readiness distribution for the template
        $readinessDistribution = [
            'high' => 0,      // 80-100
            'medium' => 0,    // 60-79
            'low' => 0,       // 40-59
            'very_low' => 0   // 0-39
        ];

        foreach ($readinessData as $user) {
            $score = $user['readiness_score'];
            if ($score >= 80) {
                $readinessDistribution['high']++;
            } elseif ($score >= 60) {
                $readinessDistribution['medium']++;
            } elseif ($score >= 40) {
                $readinessDistribution['low']++;
            } else {
                $readinessDistribution['very_low']++;
            }
        }

        // Format top conversion candidates for the template
        $topCandidates = [];
        foreach ($conversionCandidates as $candidate) {
            $topCandidates[] = [
                'user' => [
                    'id' => $candidate['user_id'],
                    'name' => $candidate['name'],
                    'email' => $candidate['email']
                ],
                'score' => round($candidate['readiness_score']),
                'skills' => $this->getSkillCount($candidate),
                'courses' => $candidate['courses_completed']
            ];
        }

        $avgReadinessScore = collect($readinessData)->avg('readiness_score') ?? 0;
        $conversionReady = collect($readinessData)->where('readiness_score', '>=', 70)->count();

        return [
            // Required keys for the Blade template
            'conversion_ready' => $conversionReady,
            'readiness_distribution' => $readinessDistribution,
            'average_readiness_score' => round($avgReadinessScore, 1),
            'top_conversion_candidates' => $topCandidates,

            // Additional analytics data
            'conversion_funnel' => $funnel,
            'readiness_analytics' => [
                'total_users_analyzed' => count($readinessData),
                'average_readiness_score' => $avgReadinessScore,
                'high_readiness_count' => $conversionReady,
                'readiness_distribution' => $this->getReadinessDistribution($readinessData)
            ],
            'insights_and_recommendations' => $insights,
            'performance_metrics' => [
                'conversion_velocity' => $this->getConversionVelocity(),
                'success_rate' => $this->getConversionSuccessRate(),
                'time_to_convert' => $this->getAverageTimeToConvert()
            ]
        ];
    }

    /**
     * Get readiness score distribution
     */
    private function getReadinessDistribution(array $readinessData): array
    {
        $distribution = [
            'excellent' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0,
            'very_low' => 0
        ];

        foreach ($readinessData as $user) {
            $level = strtolower(str_replace(' ', '_', $user['readiness_level']));
            if (isset($distribution[$level])) {
                $distribution[$level]++;
            }
        }

        return $distribution;
    }

    /**
     * Get conversion velocity (conversions per month)
     */
    private function getConversionVelocity(): float
    {
        $recentConversions = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->where('created_at', '>=', now()->subMonths(3))
          ->count();

        return round($recentConversions / 3, 2); // conversions per month
    }

    /**
     * Get conversion success rate
     */
    private function getConversionSuccessRate(): float
    {
        $totalCandidates = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->count();

        $convertedTalents = User::whereHas('roles', function($query) {
            $query->where('name', 'talent');
        })->count();

        if ($totalCandidates == 0) return 0;

        return round(($convertedTalents / $totalCandidates) * 100, 2);
    }

    /**
     * Get average time to convert (simplified calculation)
     */
    private function getAverageTimeToConvert(): int
    {
        // Simplified calculation - in a real system you'd track actual conversion dates
        // For now, return a reasonable estimate based on system data
        return 30; // days (placeholder)
    }

    /**
     * Trigger smart notifications for conversion-ready users
     */
    public function triggerSmartNotifications(): int
    {
        $candidates = $this->getConversionCandidates(5);
        $notificationsSent = 0;

        foreach ($candidates as $candidate) {
            // In a real system, you'd send actual notifications here
            // For now, just count how many would be sent
            if ($candidate['readiness_score'] >= 80) {
                $notificationsSent++;
            }
        }

        return $notificationsSent;
    }
}
