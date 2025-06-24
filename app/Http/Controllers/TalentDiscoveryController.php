<?php

namespace App\Http\Controllers;

use App\Services\TalentMatchingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TalentDiscoveryController extends Controller
{
    private TalentMatchingService $matchingService;

    public function __construct(TalentMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Display the talent discovery page
     */
    public function index()
    {
        return view('talent.discovery.index');
    }

    /**
     * Search for talents based on filters with pagination
     */
    public function search(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'min_experience' => 'nullable|integer|min:1',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string',
            'per_page' => 'nullable|integer|min:5|max:50'
        ]);

        $perPage = $filters['per_page'] ?? 12;
        $talents = $this->matchingService->discoverTalents($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $talents->values(),
            'total' => $talents->count(),
            'filters_applied' => $filters,
            'per_page' => $perPage
        ]);
    }

    /**
     * Find talents matching specific project requirements with optimization
     */
    public function match(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requirements' => 'required|string',
            'skills' => 'nullable|array',
            'skills.*.name' => 'required|string',
            'skills.*.level' => 'nullable|string|in:beginner,intermediate,advanced',
            'limit' => 'nullable|integer|min:5|max:50'
        ]);

        // Use structured skills if provided, otherwise parse requirements string
        $requirements = $validated['skills'] ?? $validated['requirements'];
        $limit = $validated['limit'] ?? 20;

        $matches = $this->matchingService->findMatchingTalents($requirements, $limit);

        return response()->json([
            'success' => true,
            'data' => $matches->values(),
            'total' => $matches->count(),
            'requirements' => $requirements,
            'limit' => $limit
        ]);
    }    /**
     * Get talent recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $recruiterId = Auth::id() ?? 1; // Default to 1 if not authenticated
        $limit = $request->input('limit', 10);

        $recommendations = $this->matchingService->getRecommendations($recruiterId, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations->values(),
            'total' => $recommendations->count(),
        ]);
    }

    /**
     * Get detailed talent profile
     */
    public function show($talentId): JsonResponse
    {
        $talent = \App\Models\User::where('id', $talentId)
            ->where('available_for_scouting', true)
            ->where('is_active_talent', true)
            ->first();

        if (!$talent) {
            return response()->json([
                'success' => false,
                'message' => 'Talent not found or not available for scouting.',
            ], 404);
        }

        // Use the service to build detailed profile
        $profile = $this->matchingService->discoverTalents(['id' => $talentId])->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to build talent profile.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $profile,
        ]);
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'min_experience' => 'nullable|integer|min:1',
            'specializations' => 'nullable|array',
            'availability' => 'nullable|string|in:available,busy',
            'sort_by' => 'nullable|string|in:match_score,experience,recent',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);

        $talents = $this->matchingService->discoverTalents($filters);

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'experience';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        switch ($sortBy) {
            case 'experience':
                $talents = $sortOrder === 'desc'
                    ? $talents->sortByDesc('skill_count')
                    : $talents->sortBy('skill_count');
                break;
            case 'recent':
                $talents = $sortOrder === 'desc'
                    ? $talents->sortByDesc('last_activity')
                    : $talents->sortBy('last_activity');
                break;
            default:
                // Keep current order
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $talents->values(),
            'total' => $talents->count(),
            'filters_applied' => $filters,
        ]);
    }

    /**
     * Get analytics for talent discovery
     */
    public function analytics(): JsonResponse
    {
        $allTalents = $this->matchingService->discoverTalents([]);

        $analytics = [
            'total_talents' => $allTalents->count(),
            'skill_distribution' => $this->getSkillDistribution($allTalents),
            'level_distribution' => $this->getLevelDistribution($allTalents),
            'specialization_distribution' => $this->getSpecializationDistribution($allTalents),
            'experience_distribution' => $this->getExperienceDistribution($allTalents),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    private function getSkillDistribution($talents): array
    {
        $skills = [];
        foreach ($talents as $talent) {
            foreach ($talent['skills'] as $skill) {
                $skillName = $skill['name'];
                if (!isset($skills[$skillName])) {
                    $skills[$skillName] = 0;
                }
                $skills[$skillName]++;
            }
        }

        arsort($skills);
        return array_slice($skills, 0, 10); // Top 10 skills
    }

    private function getLevelDistribution($talents): array
    {
        $levels = ['beginner' => 0, 'intermediate' => 0, 'advanced' => 0];

        foreach ($talents as $talent) {
            foreach ($talent['skill_levels'] as $level => $count) {
                $levels[$level] += $count;
            }
        }

        return $levels;
    }

    private function getSpecializationDistribution($talents): array
    {
        $specializations = [];

        foreach ($talents as $talent) {
            foreach ($talent['specializations'] as $spec) {
                if (!isset($specializations[$spec])) {
                    $specializations[$spec] = 0;
                }
                $specializations[$spec]++;
            }
        }

        arsort($specializations);
        return $specializations;
    }

    private function getExperienceDistribution($talents): array
    {
        $experience = ['beginner' => 0, 'intermediate' => 0, 'expert' => 0];

        foreach ($talents as $talent) {
            $level = $talent['experience_level'];
            $experience[$level]++;
        }

        return $experience;
    }
}
