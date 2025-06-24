<?php

namespace App\Services;

/**
 * Mock LMS Data Service
 *
 * This service simulates LMS data and scoring logic
 * for independent development. Later, replace with actual LMS integration.
 */
class MockLMSDataService
{
    /**
     * Mock LMS overall score generation
     * (Replace with actual LMS API call later)
     */
    public function generateOverallScore($userId)
    {
        // Simulate LMS scoring algorithm
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return 0;
        }

        $skills = $user->getTalentSkillsArray();

        if (!$skills) return 0;

        // Mock scoring based on skill count and categories
        $baseScore = count($skills) * 15; // 15 points per skill

        // Bonus for high-demand skills
        $bonusSkills = ['PHP', 'Laravel', 'React', 'Vue.js', 'Python', 'JavaScript'];
        $bonus = 0;
        foreach ($skills as $skill) {
            if (in_array($skill, $bonusSkills)) {
                $bonus += 10;
            }
        }

        return min(100, $baseScore + $bonus);
    }

    /**
     * Mock skill categorization
     * (Replace with LMS skill taxonomy later)
     */
    public function categorizeSkills($skills)
    {
        $categories = [
            'Frontend' => ['JavaScript', 'React', 'Vue.js', 'Angular', 'CSS', 'HTML'],
            'Backend' => ['PHP', 'Laravel', 'Node.js', 'Python', 'Django', 'Express'],
            'Database' => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            'DevOps' => ['Docker', 'AWS', 'Linux', 'Git', 'CI/CD'],
            'Mobile' => ['React Native', 'Flutter', 'Swift', 'Kotlin']
        ];

        $result = [];
        foreach ($skills as $skill) {
            foreach ($categories as $category => $categorySkills) {
                if (in_array($skill, $categorySkills)) {
                    $result[$category][] = $skill;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Mock market demand analysis
     * (Replace with real job market API later)
     */
    public function getMarketDemand($skill)
    {
        $demandMap = [
            'PHP' => 85,
            'Laravel' => 78,
            'JavaScript' => 92,
            'React' => 88,
            'Vue.js' => 75,
            'Python' => 90,
            'Node.js' => 82,
            'MySQL' => 80,
            'Docker' => 85,
            'AWS' => 95
        ];

        return $demandMap[$skill] ?? rand(40, 70);
    }

    /**
     * Mock learning progress data
     * (Replace with LMS course completion API later)
     */
    public function getLearningProgress($userId)
    {
        return [
            'completed_courses' => rand(5, 25),
            'total_hours' => rand(50, 300),
            'certificates' => rand(2, 8),
            'avg_score' => rand(75, 95),
            'learning_velocity' => rand(3, 12) // courses per month
        ];
    }

    /**
     * Mock talent readiness score
     * (Replace with LMS assessment results later)
     */
    public function getTalentReadiness($userId)
    {
        $progress = $this->getLearningProgress($userId);
        $overallScore = $this->generateOverallScore($userId);

        // Weighted calculation
        $progressWeight = 0.4;
        $skillWeight = 0.6;

        $progressScore = min(100, ($progress['completed_courses'] * 3) +
                                  ($progress['certificates'] * 8));

        return round(($progressScore * $progressWeight) + ($overallScore * $skillWeight));
    }

    /**
     * Generate mock talent profile for integration template
     */
    public function generateTalentProfile($userId)
    {
        $user = \App\Models\User::find($userId);

        if (!$user) return null;

        $skills = $user->getTalentSkillsArray();

        return [
            'user_id' => $userId,
            'overall_score' => $this->generateOverallScore($userId),
            'readiness_score' => $this->getTalentReadiness($userId),
            'skills' => $skills,
            'skill_categories' => $this->categorizeSkills($skills),
            'learning_progress' => $this->getLearningProgress($userId),
            'market_alignment' => $this->calculateMarketAlignment($skills),
            'recommendations' => $this->generateRecommendations($skills),
            'integration_ready' => true, // Flag for when LMS is connected
            'data_source' => 'mock' // Will be 'lms' when integrated
        ];
    }

    private function calculateMarketAlignment($skills)
    {
        if (empty($skills)) return 0;

        $totalDemand = 0;
        foreach ($skills as $skill) {
            $totalDemand += $this->getMarketDemand($skill);
        }

        return round($totalDemand / count($skills));
    }

    private function generateRecommendations($skills)
    {
        $trending = ['React', 'Python', 'AWS', 'Docker', 'TypeScript'];
        $recommendations = [];

        foreach ($trending as $skill) {
            if (!in_array($skill, $skills)) {
                $recommendations[] = [
                    'skill' => $skill,
                    'reason' => 'High market demand',
                    'priority' => $this->getMarketDemand($skill)
                ];
            }
        }

        return array_slice($recommendations, 0, 3);
    }
}
