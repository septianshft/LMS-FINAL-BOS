<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\QuizAttempt;
use App\Models\TalentRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdvancedSkillAnalyticsService
{
    /**
     * Get comprehensive skill analytics for dashboard
     */
    public function getSkillAnalytics(): array
    {
        return [
            'skill_categories' => $this->getSkillCategoryDistribution(),
            'market_demand_analysis' => $this->getMarketDemandAnalysis(),
            'talent_conversion_metrics' => $this->getTalentConversionMetrics(),
            'skill_progression_trends' => $this->getSkillProgressionTrends(),
            'top_performing_skills' => $this->getTopPerformingSkills(),
            'conversion_funnel' => $this->getConversionFunnelMetrics(),
        ];
    }

    /**
     * Skill category distribution across all talents
     */
    public function getSkillCategoryDistribution(): array
    {
        $talents = User::where('available_for_scouting', true)
            ->whereNotNull('talent_skills')
            ->get();

        $categories = [];
        foreach ($talents as $talent) {
            // Safely decode talent_skills if it's stored as JSON string
            $talentSkills = $this->getTalentSkills($talent);

            foreach ($talentSkills as $skill) {
                if (is_array($skill)) {
                    $category = $skill['category'] ?? 'General Technology';
                    $categories[$category] = ($categories[$category] ?? 0) + 1;
                }
            }
        }

        arsort($categories);
        return $categories;
    }

    /**
     * Market demand analysis with hiring trends
     */
    public function getMarketDemandAnalysis(): array
    {
        $skillsByDemand = [
            'Very High' => [],
            'High' => [],
            'Medium' => [],
            'Low' => []
        ];

        $talents = User::where('available_for_scouting', true)
            ->whereNotNull('talent_skills')
            ->get();

        foreach ($talents as $talent) {
            // Safely decode talent_skills if it's stored as JSON string
            $talentSkills = $this->getTalentSkills($talent);

            foreach ($talentSkills as $skill) {
                if (is_array($skill)) {
                    $demand = $skill['market_demand'] ?? 'Medium';
                    $skillsByDemand[$demand][] = $skill;
                }
            }
        }

        return [
            'distribution' => array_map('count', $skillsByDemand),
            'top_demanded_skills' => $this->getTopDemandedSkills($talents),
            'supply_demand_ratio' => $this->calculateSupplyDemandRatio($skillsByDemand)
        ];
    }

    /**
     * Talent conversion metrics and funnel analysis
     */
    public function getTalentConversionMetrics(): array
    {
        $totalTrainees = User::whereHas('roles', function($q) {
            $q->where('name', 'trainee');
        })->count();

        $totalTalents = User::where('available_for_scouting', true)->count();

        $conversionRate = $totalTrainees > 0 ? ($totalTalents / $totalTrainees) * 100 : 0;

        $monthlyConversions = User::where('available_for_scouting', true)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as conversions')
            ->get()
            ->pluck('conversions', 'month');

        return [
            'total_trainees' => $totalTrainees,
            'total_talents' => $totalTalents,
            'conversion_rate' => round($conversionRate, 2),
            'monthly_trends' => $monthlyConversions,
            'average_skills_before_conversion' => $this->getAverageSkillsBeforeConversion(),
            'conversion_triggers' => $this->getConversionTriggers()
        ];
    }

    /**
     * Skill progression trends over time
     */
    public function getSkillProgressionTrends(): array
    {
        $talents = User::where('available_for_scouting', true)->get();

        $progressionData = [];
        foreach ($talents as $talent) {
            $skills = $this->getTalentSkills($talent);
            foreach ($skills as $skill) {
                if (is_array($skill)) {
                    $month = Carbon::parse($skill['completed_date'] ?? $skill['acquired_at'] ?? now())->format('Y-m');
                    $progressionData[$month] = ($progressionData[$month] ?? 0) + 1;
                }
            }
        }

        ksort($progressionData);

        return [
            'monthly_skill_acquisition' => $progressionData,
            'skill_velocity' => $this->calculateSkillVelocity($talents),
            'learning_patterns' => $this->analyzeLearningPatterns($talents)
        ];
    }

    /**
     * Top performing skills based on recruitment success
     */
    public function getTopPerformingSkills(): array
    {
        $requestedSkills = [];

        // Analyze talent requests to see which skills are most sought after
        $requests = TalentRequest::with('talentUser')->get();

        foreach ($requests as $request) {
            if ($request->talentUser) {
                $talentSkills = $this->getTalentSkills($request->talentUser);
                foreach ($talentSkills as $skill) {
                    if (is_array($skill)) {
                        $skillName = $skill['name'] ?? 'Unknown Skill';
                        $requestedSkills[$skillName] = ($requestedSkills[$skillName] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($requestedSkills);

        return [
            'most_requested' => array_slice($requestedSkills, 0, 10, true),
            'success_rate_by_skill' => $this->calculateSkillSuccessRates($requestedSkills),
            'emerging_skills' => $this->identifyEmergingSkills()
        ];
    }

    /**
     * Conversion funnel metrics
     */
    public function getConversionFunnelMetrics(): array
    {
        $totalUsers = User::count();
        $registeredTrainees = User::whereHas('roles', function($q) {
            $q->where('name', 'trainee');
        })->count();

        $courseCompletions = User::whereHas('courses')->count();
        $skillAcquisitions = User::whereNotNull('talent_skills')->count();
        $talentOptIns = User::where('available_for_scouting', true)->count();
        $successfulPlacements = TalentRequest::where('status', 'completed')->count();

        return [
            'funnel_stages' => [
                'total_users' => $totalUsers,
                'registered_trainees' => $registeredTrainees,
                'course_completions' => $courseCompletions,
                'skill_acquisitions' => $skillAcquisitions,
                'talent_opt_ins' => $talentOptIns,
                'successful_placements' => $successfulPlacements
            ],
            'conversion_rates' => [
                'registration_to_course' => $registeredTrainees > 0 ? ($courseCompletions / $registeredTrainees) * 100 : 0,
                'course_to_skills' => $courseCompletions > 0 ? ($skillAcquisitions / $courseCompletions) * 100 : 0,
                'skills_to_talent' => $skillAcquisitions > 0 ? ($talentOptIns / $skillAcquisitions) * 100 : 0,
                'talent_to_placement' => $talentOptIns > 0 ? ($successfulPlacements / $talentOptIns) * 100 : 0
            ]
        ];
    }

    /**
     * Learning engagement correlation metrics
     */
    public function getLearningEngagementMetrics(): array
    {
        $talents = User::where('available_for_scouting', true)->get();

        $correlationData = [
            'avg_skills_by_category' => []
        ];

        foreach ($talents as $talent) {
            $talentSkills = $this->getTalentSkills($talent);

            // Analyze by primary skill category
            if (!empty($talentSkills) && is_array($talentSkills[0])) {
                $primaryCategory = $talentSkills[0]['category'] ?? 'General';
                if (!isset($correlationData['avg_skills_by_category'][$primaryCategory])) {
                    $correlationData['avg_skills_by_category'][$primaryCategory] = [];
                }
                $correlationData['avg_skills_by_category'][$primaryCategory][] = count($talentSkills);
            }
        }

        // Calculate averages
        foreach ($correlationData['avg_skills_by_category'] as $category => &$skillCounts) {
            $skillCounts = round(array_sum($skillCounts) / count($skillCounts), 2);
        }

        return $correlationData;
    }

    /**
     * Helper methods for complex calculations
     */
    private function getTopDemandedSkills($talents): array
    {
        $skillCounts = [];
        foreach ($talents as $talent) {
            foreach ($this->getTalentSkills($talent) as $skill) {
                if (is_array($skill) && ($skill['market_demand'] ?? 'Medium') === 'Very High') {
                    $skillName = $skill['name'] ?? 'Unknown Skill';
                    $skillCounts[$skillName] = ($skillCounts[$skillName] ?? 0) + 1;
                }
            }
        }
        arsort($skillCounts);
        return array_slice($skillCounts, 0, 10, true);
    }

    private function calculateSupplyDemandRatio($skillsByDemand): array
    {
        $ratios = [];
        foreach ($skillsByDemand as $demand => $skills) {
            $supply = count($skills);
            $demandScore = ['Very High' => 4, 'High' => 3, 'Medium' => 2, 'Low' => 1][$demand];
            $ratios[$demand] = $supply > 0 ? round($demandScore / $supply, 2) : 0;
        }
        return $ratios;
    }

    private function getAverageSkillsBeforeConversion(): float
    {
        $talents = User::where('available_for_scouting', true)->get();
        $totalSkills = $talents->sum(function($talent) {
            return count($this->getTalentSkills($talent));
        });
        return $talents->count() > 0 ? round($totalSkills / $talents->count(), 2) : 0;
    }

    private function getConversionTriggers(): array
    {
        // Analyze common patterns that lead to talent conversion
        return [
            'course_completion_threshold' => 3,
            'skill_count_threshold' => 5,
            'high_demand_skills' => ['Backend Development', 'Data Science', 'Cybersecurity'],
            'completion_rate_threshold' => 80
        ];
    }

    private function calculateSkillVelocity($talents): array
    {
        $velocityData = [];
        foreach ($talents as $talent) {
            $skills = $this->getTalentSkills($talent);
            if (count($skills) >= 2) {
                $dates = array_map(function($skill) {
                    if (is_array($skill)) {
                        return Carbon::parse($skill['completed_date'] ?? $skill['acquired_at'] ?? now());
                    }
                    return Carbon::now();
                }, $skills);

                sort($dates);
                $daysBetween = $dates[count($dates) - 1]->diffInDays($dates[0]);
                $velocity = $daysBetween > 0 ? count($skills) / $daysBetween : 0;
                $velocityData[] = $velocity;
            }
        }        rsort($velocityData);

        return [
            'average_skills_per_day' => count($velocityData) > 0 ? round(array_sum($velocityData) / count($velocityData), 3) : 0,
            'fastest_learners' => array_slice($velocityData, 0, 5)
        ];
    }

    private function analyzeLearningPatterns($talents): array
    {
        $patterns = [
            'weekend_learning' => 0,
            'weekday_learning' => 0,
            'sequential_categories' => 0,
            'diverse_categories' => 0
        ];

        foreach ($talents as $talent) {
            $skills = $this->getTalentSkills($talent);
            foreach ($skills as $skill) {
                if (is_array($skill)) {
                    $date = Carbon::parse($skill['completed_date'] ?? $skill['acquired_at'] ?? now());
                    if ($date->isWeekend()) {
                        $patterns['weekend_learning']++;
                    } else {
                        $patterns['weekday_learning']++;
                    }
                }
            }

            // Analyze category diversity
            $categories = [];
            foreach ($skills as $skill) {
                if (is_array($skill) && isset($skill['category'])) {
                    $categories[] = $skill['category'];
                }
            }
            $categories = array_unique($categories);
            if (count($categories) > 1) {
                $patterns['diverse_categories']++;
            } else {
                $patterns['sequential_categories']++;
            }
        }

        return $patterns;
    }

    private function calculateSkillSuccessRates($requestedSkills): array
    {
        $successRates = [];
        $completedRequests = TalentRequest::where('status', 'completed')->with('talentUser')->get();

        foreach ($requestedSkills as $skill => $totalRequests) {
            $successfulRequests = $completedRequests->filter(function($request) use ($skill) {
                if (!$request->talentUser) return false;
                $talentSkills = $this->getTalentSkills($request->talentUser);
                return collect($talentSkills)->contains(function($skillItem) use ($skill) {
                    return is_array($skillItem) && ($skillItem['name'] ?? '') === $skill;
                });
            })->count();

            $successRates[$skill] = $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 2) : 0;
        }

        return $successRates;
    }

    private function identifyEmergingSkills(): array
    {
        // Skills acquired in the last 3 months
        $recentSkills = [];
        $cutoffDate = Carbon::now()->subMonths(3);

        $talents = User::where('available_for_scouting', true)->get();
        foreach ($talents as $talent) {
            foreach ($this->getTalentSkills($talent) as $skill) {
                if (is_array($skill)) {
                    $acquiredDate = Carbon::parse($skill['completed_date'] ?? $skill['acquired_at'] ?? now());
                    if ($acquiredDate->gte($cutoffDate)) {
                        $skillName = $skill['name'] ?? 'Unknown Skill';
                        $recentSkills[$skillName] = ($recentSkills[$skillName] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($recentSkills);
        return array_slice($recentSkills, 0, 5, true);
    }

    /**
     * Safely get talent skills as an array, handling JSON string and null cases
     */
    private function getTalentSkills(User $talent): array
    {
        $skills = $talent->getTalentSkillsArray();
        return $this->normalizeTalentSkills($skills);
    }

    /**
     * Normalize talent skills to ensure consistent structure
     */
    private function normalizeTalentSkills(array $skills): array
    {
        $normalized = [];

        foreach ($skills as $skill) {
            // If skill is a string, convert to basic array format
            if (is_string($skill)) {
                $normalized[] = [
                    'skill_name' => $skill,
                    'proficiency' => 'intermediate',
                    'completed_date' => now()->toDateString()
                ];
            } elseif (is_array($skill)) {
                // Ensure required keys exist with defaults
                $normalized[] = [
                    'skill_name' => $skill['skill_name'] ?? $skill['name'] ?? 'Unknown Skill',
                    'proficiency' => $skill['proficiency'] ?? $skill['level'] ?? 'intermediate',
                    'completed_date' => $skill['completed_date'] ?? $skill['acquired_at'] ?? now()->toDateString()
                ];
            }
            // Skip non-string, non-array items
        }

        return $normalized;
    }
}
