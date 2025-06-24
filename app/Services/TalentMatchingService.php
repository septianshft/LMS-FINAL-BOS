<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\TalentRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TalentMatchingService
{
    /**
     * Discover available talents based on filters with pagination and optimization
     */
    public function discoverTalents($filters = [], $perPage = 12): Collection
    {
        // Create cache key based on filters
        $cacheKey = 'talent_discovery_' . md5(serialize($filters)) . "_{$perPage}";

        return Cache::remember($cacheKey, 300, function() use ($filters, $perPage) {
            $query = User::select(['id', 'name', 'email', 'avatar', 'talent_bio', 'portfolio_url',
                                 'talent_skills', 'experience_level', 'updated_at',
                                 'available_for_scouting', 'is_active_talent'])
                ->where('available_for_scouting', true)
                ->where('is_active_talent', true)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['trainee', 'talent']);
                });

            // Filter by skills if provided
            if (isset($filters['skills']) && !empty($filters['skills'])) {
                $skills = is_array($filters['skills']) ? $filters['skills'] : [$filters['skills']];
                $query->where(function($q) use ($skills) {
                    foreach ($skills as $skill) {
                        $q->orWhereJsonContains('talent_skills', [['skill_name' => $skill]]);
                    }
                });
            }

            // Filter by skill level if provided
            if (isset($filters['level']) && !empty($filters['level'])) {
                $query->where(function($q) use ($filters) {
                    $q->whereJsonContains('talent_skills', [['level' => strtolower($filters['level'])]]);
                });
            }

            // Filter by experience (number of completed courses/skills)
            if (isset($filters['min_experience'])) {
                $query->whereRaw('JSON_LENGTH(talent_skills) >= ?', [$filters['min_experience']]);
            }

            // Apply ordering for consistency and use database ordering
            $query->orderBy('updated_at', 'desc');

            // Use optimized chunking for large datasets to prevent memory issues
            $talents = collect();
            $targetCount = $perPage * 2;
            $query->chunk(50, function($users) use (&$talents, $targetCount) {
                foreach ($users as $user) {
                    if ($talents->count() >= $targetCount) {
                        return false; // Stop chunking
                    }
                    $talents->push($this->buildOptimizedTalentProfile($user));
                }
            });

            return $talents->take($perPage * 3); // Return more results for better filtering
        });
    }

    /**
     * Find matching talents for specific project requirements with optimized queries
     */
    public function findMatchingTalents($projectRequirements, $limit = 20): Collection
    {
        $requiredSkills = $this->parseSkillRequirements($projectRequirements);

        if (empty($requiredSkills)) {
            return collect();
        }

        // Cache key for this specific search
        $cacheKey = 'talent_matching_' . md5(serialize($requiredSkills)) . "_{$limit}";

        return Cache::remember($cacheKey, 180, function() use ($requiredSkills, $limit) {
            $query = User::select(['id', 'name', 'email', 'avatar', 'talent_skills', 'updated_at',
                                 'talent_bio', 'experience_level'])
                ->where('available_for_scouting', true)
                ->where('is_active_talent', true)
                ->whereNotNull('talent_skills')
                ->orderBy('updated_at', 'desc')
                ->limit($limit * 2); // Get more results to allow for filtering

            $talents = $query->get();

            $matchedTalents = $talents->map(function($user) use ($requiredSkills) {
                $userSkills = collect($user->getTalentSkillsArray());
                $matchScore = $this->calculateMatchScore($userSkills, $requiredSkills);

                if ($matchScore > 0) {
                    $profile = $this->buildOptimizedTalentProfile($user);
                    $profile['match_score'] = $matchScore;
                    $profile['matching_skills'] = $this->getMatchingSkills($userSkills, $requiredSkills);
                    return $profile;
                }

                return null;
            })->filter()->sortByDesc('match_score')->take($limit);

            return $matchedTalents;
        });
    }

    /**
     * Get talent recommendations for a recruiter with caching and optimization
     */
    public function getRecommendations($recruiterId, $limit = 10): Collection
    {
        $cacheKey = "talent_recommendations_{$recruiterId}_{$limit}";

        return Cache::remember($cacheKey, 600, function() use ($limit) {
            // Use database ordering instead of loading all and sorting in PHP
            $talents = User::select(['id', 'name', 'email', 'avatar', 'talent_skills', 'updated_at',
                                   'talent_bio', 'experience_level'])
                ->where('available_for_scouting', true)
                ->where('is_active_talent', true)
                ->whereNotNull('talent_skills')
                ->orderByRaw('JSON_LENGTH(talent_skills) DESC') // Order by skill count in database
                ->orderBy('updated_at', 'desc')
                ->limit($limit * 2) // Get more to account for filtering
                ->get();

            return $talents->map(function($user) {
                $profile = $this->buildOptimizedTalentProfile($user);
                $profile['recommendation_score'] = $this->calculateRecommendationScore($user);
                return $profile;
            })->sortByDesc('recommendation_score')->take($limit);
        });
    }

    /**
     * Build an optimized talent profile with minimal database queries
     */
    private function buildOptimizedTalentProfile(User $user): array
    {
        $skills = collect($user->getTalentSkillsArray());

        // Get cached availability status for better performance
        $availability = TalentRequest::getCachedTalentAvailability($user->id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'bio' => $user->talent_bio,
            'skills' => $skills->toArray(),
            'skill_count' => $skills->count(),
            'experience_level' => $user->experience_level,
            'last_activity' => $user->updated_at,
            // Cached availability data
            'availability' => $availability,
            'is_available' => $availability['available'],
            'availability_status' => $availability['status'],
            'next_available_date' => $availability['next_available_date'],
        ];
    }

    /**
     * Calculate match score between user skills and required skills
     */
    private function calculateMatchScore($userSkills, $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 0;
        }

        $userSkillNames = $userSkills->pluck('name')->map('strtolower');
        $matchedSkills = 0;
        $levelBonuses = 0;

        foreach ($requiredSkills as $required) {
            $skillName = strtolower($required['name']);

            if ($userSkillNames->contains($skillName)) {
                $matchedSkills++;

                // Bonus for matching or exceeding required level
                $userSkill = $userSkills->firstWhere('name', $required['name']);
                if ($userSkill && isset($userSkill['level'])) {
                    $levelBonus = $this->getLevelBonus($userSkill['level'], $required['level'] ?? 'beginner');
                    $levelBonuses += $levelBonus;
                }
            }
        }

        $baseScore = ($matchedSkills / count($requiredSkills)) * 100;
        $bonusScore = $levelBonuses * 10; // 10 points per level bonus

        return min(100, $baseScore + $bonusScore);
    }

    /**
     * Get matching skills between user and requirements
     */
    private function getMatchingSkills($userSkills, $requiredSkills): array
    {
        $matches = [];
        $userSkillNames = $userSkills->pluck('name')->map('strtolower');

        foreach ($requiredSkills as $required) {
            $skillName = strtolower($required['name']);

            if ($userSkillNames->contains($skillName)) {
                $userSkill = $userSkills->firstWhere('name', $required['name']);
                $matches[] = [
                    'skill' => $required['name'],
                    'user_level' => $userSkill['level'] ?? 'unknown',
                    'required_level' => $required['level'] ?? 'beginner',
                    'meets_requirement' => $this->meetsLevelRequirement(
                        $userSkill['level'] ?? 'beginner',
                        $required['level'] ?? 'beginner'
                    ),
                ];
            }
        }

        return $matches;
    }

    /**
     * Parse skill requirements from various formats
     */
    private function parseSkillRequirements($requirements): array
    {
        if (is_string($requirements)) {
            // Simple comma-separated format: "JavaScript, Python, React"
            $skills = explode(',', $requirements);
            return array_map(function($skill) {
                return ['name' => trim($skill), 'level' => 'beginner'];
            }, $skills);
        }

        if (is_array($requirements)) {
            // Already formatted array
            return $requirements;
        }

        return [];
    }

    /**
     * Calculate experience level based on skills
     */
    private function calculateExperienceLevel($skills): string
    {
        $skillCount = $skills->count();
        $advancedSkills = $skills->where('level', 'advanced')->count();
        $intermediateSkills = $skills->where('level', 'intermediate')->count();

        if ($advancedSkills >= 3 || $skillCount >= 10) {
            return 'expert';
        } elseif ($advancedSkills >= 1 || $intermediateSkills >= 3 || $skillCount >= 5) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Get skill level distribution
     */
    private function getSkillLevelDistribution($skills): array
    {
        return [
            'beginner' => $skills->where('level', 'beginner')->count(),
            'intermediate' => $skills->where('level', 'intermediate')->count(),
            'advanced' => $skills->where('level', 'advanced')->count(),
        ];
    }

    /**
     * Extract specializations from skills
     */
    private function extractSpecializations($skills): array
    {
        // Group skills by common categories
        $categories = [
            'frontend' => ['javascript', 'react', 'vue', 'angular', 'html', 'css'],
            'backend' => ['php', 'python', 'java', 'node.js', 'laravel', 'django'],
            'data' => ['python', 'r', 'sql', 'data science', 'machine learning'],
            'mobile' => ['android', 'ios', 'react native', 'flutter'],
            'design' => ['ui/ux', 'photoshop', 'figma', 'design'],
        ];

        $specializations = [];
        $skillNames = $skills->pluck('name')->map('strtolower');

        foreach ($categories as $category => $keywords) {
            $matches = 0;
            foreach ($keywords as $keyword) {
                if ($skillNames->contains(function($skill) use ($keyword) {
                    return str_contains($skill, $keyword);
                })) {
                    $matches++;
                }
            }

            if ($matches >= 2) {
                $specializations[] = $category;
            }
        }

        return $specializations;
    }

    /**
     * Calculate recommendation score
     */
    private function calculateRecommendationScore($user): float
    {
        $skills = collect($user->getTalentSkillsArray());
        $skillCount = $skills->count();
        $levelVariety = count(array_unique($skills->pluck('level')->toArray()));
        $recentActivity = $user->updated_at->diffInDays(now());

        // Score based on skill diversity, level variety, and recent activity
        $skillScore = min(50, $skillCount * 5);
        $varietyScore = $levelVariety * 10;
        $activityScore = max(0, 40 - $recentActivity);

        return $skillScore + $varietyScore + $activityScore;
    }

    /**
     * Get level bonus for matching or exceeding requirements
     */
    private function getLevelBonus($userLevel, $requiredLevel): float
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];

        $userLevelNum = $levels[strtolower($userLevel)] ?? 1;
        $requiredLevelNum = $levels[strtolower($requiredLevel)] ?? 1;

        return max(0, $userLevelNum - $requiredLevelNum);
    }

    /**
     * Check if user skill level meets requirement
     */
    private function meetsLevelRequirement($userLevel, $requiredLevel): bool
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];

        $userLevelNum = $levels[strtolower($userLevel)] ?? 1;
        $requiredLevelNum = $levels[strtolower($requiredLevel)] ?? 1;

        return $userLevelNum >= $requiredLevelNum;
    }

    /**
     * Check if talent is available for new projects during specific dates
     */
    public function isTalentAvailable($talentUserId, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $endDate = $endDate ?: $startDate->copy()->addMonths(1); // Default 1 month project

        // Get blocking requests for this talent
        $blockingRequests = \App\Models\TalentRequest::getActiveBlockingRequestsForTalent($talentUserId);

        if ($blockingRequests->isEmpty()) {
            return [
                'available' => true,
                'status' => 'Available',
                'next_available_date' => null,
                'blocking_projects' => []
            ];
        }

        // Check if proposed dates conflict
        $hasConflict = $blockingRequests->filter(function($request) use ($startDate, $endDate) {
            return $request->project_start_date <= $endDate &&
                   $request->project_end_date >= $startDate;
        })->isNotEmpty();

        if ($hasConflict) {
            $nextAvailable = $blockingRequests->max('project_end_date');
            return [
                'available' => false,
                'status' => 'Busy until ' . $nextAvailable->format('M d, Y'),
                'next_available_date' => $nextAvailable->copy()->addDay(),
                'blocking_projects' => $blockingRequests->map(function($request) {
                    return [
                        'title' => $request->project_title,
                        'company' => $request->recruiter->user->name ?? 'Unknown',
                        'start_date' => $request->project_start_date->format('M d, Y'),
                        'end_date' => $request->project_end_date->format('M d, Y'),
                    ];
                })->toArray()
            ];
        }

        return [
            'available' => true,
            'status' => 'Available',
            'next_available_date' => null,
            'blocking_projects' => []
        ];
    }
}
