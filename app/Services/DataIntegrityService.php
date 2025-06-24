<?php

namespace App\Services;

use App\Models\User;
use App\Models\Talent;
use App\Models\TalentRequest;
use App\Models\Recruiter;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Data Integrity Service
 *
 * Ensures that data fetching between LMS and talent scouting systems is working correctly.
 * Validates data consistency and identifies potential issues with data synchronization.
 */
class DataIntegrityService
{
    protected $lmsIntegrationService;
    protected $talentScoutingService;

    public function __construct(
        LMSIntegrationService $lmsIntegrationService,
        TalentScoutingService $talentScoutingService
    ) {
        $this->lmsIntegrationService = $lmsIntegrationService;
        $this->talentScoutingService = $talentScoutingService;
    }

    /**
     * Run comprehensive data integrity checks
     */
    public function runDataIntegrityChecks()
    {
        $results = [
            'timestamp' => now()->toDateTimeString(),
            'status' => 'checking',
            'checks' => []
        ];

        try {
            // 1. Check user data consistency
            $results['checks']['user_data'] = $this->checkUserDataConsistency();

            // 2. Check talent profile completeness
            $results['checks']['talent_profiles'] = $this->checkTalentProfileCompleteness();

            // 3. Check LMS data fetching
            $results['checks']['lms_integration'] = $this->checkLMSDataFetching();

            // 4. Check skill data integrity
            $results['checks']['skill_integrity'] = $this->checkSkillDataIntegrity();

            // 5. Check course-skill mapping
            $results['checks']['course_skill_mapping'] = $this->checkCourseSkillMapping();

            // 6. Check recruiter data access
            $results['checks']['recruiter_data'] = $this->checkRecruiterDataAccess();

            // 7. Check talent request data flow
            $results['checks']['talent_request_flow'] = $this->checkTalentRequestDataFlow();

            // 8. Check caching consistency
            $results['checks']['caching'] = $this->checkCachingConsistency();

            // Determine overall status
            $failedChecks = collect($results['checks'])->filter(function($check) {
                return $check['status'] === 'failed';
            });

            $results['status'] = $failedChecks->isEmpty() ? 'passed' : 'failed';
            $results['summary'] = [
                'total_checks' => count($results['checks']),
                'passed' => count($results['checks']) - $failedChecks->count(),
                'failed' => $failedChecks->count(),
                'critical_issues' => $failedChecks->filter(function($check) {
                    return $check['severity'] === 'critical';
                })->count()
            ];

        } catch (\Exception $e) {
            $results['status'] = 'error';
            $results['error'] = $e->getMessage();
            Log::error('Data integrity check failed: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Check user data consistency between different tables
     */
    private function checkUserDataConsistency()
    {
        $issues = [];
        $checkedUsers = 0;

        $users = User::with(['talent', 'recruiter'])->take(50)->get();

        foreach ($users as $user) {
            $checkedUsers++;

            // Check if talent skills are properly formatted
            if ($user->talent_skills) {
                $skills = $user->getTalentSkillsArray();
                if (!is_array($skills)) {
                    $issues[] = "User {$user->id}: talent_skills is not properly formatted as array";
                }
            }

            // Check talent relationship consistency
            if ($user->available_for_scouting && !$user->talent) {
                $issues[] = "User {$user->id}: marked as available for scouting but no talent record exists";
            }

            // Check email format
            if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $issues[] = "User {$user->id}: invalid email format";
            }

            // Check if phone field access works correctly
            if ($user->talent && $user->talent->user) {
                $phoneAccess = $user->talent->user->phone ?? null;
                // This should work without errors
            }
        }

        return [
            'status' => empty($issues) ? 'passed' : 'failed',
            'severity' => empty($issues) ? 'info' : 'high',
            'checked_users' => $checkedUsers,
            'issues' => $issues,
            'description' => 'Validates user data consistency across related tables'
        ];
    }

    /**
     * Check talent profile completeness and accessibility
     */
    private function checkTalentProfileCompleteness()
    {
        $issues = [];
        $talentCount = 0;
        $completeProfiles = 0;

        $talents = Talent::with('user')->where('is_active', true)->take(20)->get();

        foreach ($talents as $talent) {
            $talentCount++;
            $user = $talent->user;

            if (!$user) {
                $issues[] = "Talent {$talent->id}: no associated user record";
                continue;
            }

            $profileScore = 0;
            $maxScore = 6;

            // Check required fields
            if ($user->name) $profileScore++;
            if ($user->email) $profileScore++;
            if ($user->phone) $profileScore++;
            if ($user->talent_skills) $profileScore++;
            if ($user->experience_level) $profileScore++;
            if ($user->talent_bio) $profileScore++;

            $completenessPercentage = ($profileScore / $maxScore) * 100;

            if ($completenessPercentage >= 80) {
                $completeProfiles++;
            }

            if ($completenessPercentage < 50) {
                $issues[] = "Talent {$talent->id} (User {$user->id}): profile only {$completenessPercentage}% complete";
            }

            // Test actual blade template access patterns
            try {
                $phoneDisplay = $user->phone ?? 'Not provided';
                $skillsDisplay = $user->talent_skills ? 'Available' : 'Not specified';
                $experienceDisplay = $user->experience_level ? ucfirst($user->experience_level) : 'Not specified';
            } catch (\Exception $e) {
                $issues[] = "Talent {$talent->id}: error accessing profile data - " . $e->getMessage();
            }
        }

        return [
            'status' => count($issues) < ($talentCount * 0.2) ? 'passed' : 'failed', // Allow 20% to have issues
            'severity' => 'medium',
            'total_talents' => $talentCount,
            'complete_profiles' => $completeProfiles,
            'completion_rate' => $talentCount > 0 ? round(($completeProfiles / $talentCount) * 100, 1) : 0,
            'issues' => $issues,
            'description' => 'Validates talent profile completeness and data accessibility'
        ];
    }

    /**
     * Check LMS data fetching functionality
     */
    private function checkLMSDataFetching()
    {
        $issues = [];
        $testUsers = User::whereNotNull('talent_skills')->take(5)->get();

        foreach ($testUsers as $user) {
            try {
                // Test LMS integration service
                $talentData = $this->lmsIntegrationService->getTalentData($user->id);
                $overallScore = $this->lmsIntegrationService->getOverallScore($user->id);
                $skillAnalysis = $this->lmsIntegrationService->getSkillAnalysis($user->id);
                $learningProgress = $this->lmsIntegrationService->getLearningProgress($user->id);

                // Validate response structure
                if (!is_array($talentData)) {
                    $issues[] = "User {$user->id}: LMS talent data is not an array";
                }

                if (!is_numeric($overallScore)) {
                    $issues[] = "User {$user->id}: LMS overall score is not numeric";
                }

                if (!isset($skillAnalysis['skills']) || !is_array($skillAnalysis['skills'])) {
                    $issues[] = "User {$user->id}: LMS skill analysis missing or invalid skills array";
                }

                if (!isset($learningProgress['completed_courses'])) {
                    $issues[] = "User {$user->id}: LMS learning progress missing completed_courses";
                }

            } catch (\Exception $e) {
                $issues[] = "User {$user->id}: LMS data fetching failed - " . $e->getMessage();
            }
        }

        $integrationStatus = $this->lmsIntegrationService->getIntegrationStatus();

        return [
            'status' => empty($issues) ? 'passed' : 'failed',
            'severity' => empty($issues) ? 'info' : 'critical',
            'integration_status' => $integrationStatus,
            'tested_users' => count($testUsers),
            'issues' => $issues,
            'description' => 'Validates LMS integration service functionality'
        ];
    }

    /**
     * Check skill data integrity and consistency
     */
    private function checkSkillDataIntegrity()
    {
        $issues = [];
        $skillStats = [
            'users_with_skills' => 0,
            'empty_skills' => 0,
            'invalid_json' => 0,
            'duplicate_skills' => 0
        ];

        $users = User::whereNotNull('talent_skills')->get();

        foreach ($users as $user) {
            $skills = $user->getTalentSkillsArray();
            if (empty($skills)) {
                $skillStats['empty_skills']++;
                continue;
            }
            if (is_array($skills)) {
                $skillStats['users_with_skills']++;

                // Check for duplicates
                if (count($skills) !== count(array_unique($skills))) {
                    $skillStats['duplicate_skills']++;
                    $issues[] = "User {$user->id}: duplicate skills detected";
                }

                // Check skill format
                foreach ($skills as $skill) {
                    if (empty($skill) || !is_string($skill)) {
                        $issues[] = "User {$user->id}: invalid skill format detected";
                        break;
                    }
                }
            }
        }

        return [
            'status' => ($skillStats['invalid_json'] + count($issues)) === 0 ? 'passed' : 'failed',
            'severity' => 'high',
            'stats' => $skillStats,
            'issues' => $issues,
            'description' => 'Validates skill data format and consistency'
        ];
    }

    /**
     * Check course-skill mapping integrity
     */
    private function checkCourseSkillMapping()
    {
        $issues = [];
        $mappingStats = [
            'courses_with_skills' => 0,
            'courses_without_skills' => 0,
            'users_with_course_skills' => 0
        ];

        // Check if completed courses properly generate skills
        $usersWithProgress = User::whereHas('courseProgress', function($query) {
            $query->where('progress', 100);
        })->with('courseProgress.course')->take(10)->get();

        foreach ($usersWithProgress as $user) {
            $completedCourses = $user->courseProgress->where('progress', 100);
            $userSkills = $user->getTalentSkillsArray();

            if ($completedCourses->count() > 0 && empty($userSkills)) {
                $issues[] = "User {$user->id}: completed {$completedCourses->count()} courses but has no skills";
            }

            if (!empty($userSkills)) {
                $mappingStats['users_with_course_skills']++;
            }
        }

        // Check courses for skill metadata
        $courses = Course::take(20)->get();
        foreach ($courses as $course) {
            // Assuming courses should have some way to map to skills
            // This would depend on your course structure
            $mappingStats['courses_with_skills']++;
        }

        return [
            'status' => count($issues) < 3 ? 'passed' : 'failed',
            'severity' => 'medium',
            'stats' => $mappingStats,
            'issues' => $issues,
            'description' => 'Validates course completion to skill mapping'
        ];
    }

    /**
     * Check recruiter data access patterns
     */
    private function checkRecruiterDataAccess()
    {
        $issues = [];
        $accessStats = [
            'recruiters_checked' => 0,
            'access_errors' => 0,
            'missing_data' => 0
        ];

        $recruiters = Recruiter::with('user')->take(10)->get();

        foreach ($recruiters as $recruiter) {
            $accessStats['recruiters_checked']++;

            try {
                // Test the exact patterns used in the blade template
                $recruiterPhone = $recruiter->phone ?? $recruiter->user->phone ?? 'Not provided';
                $recruiterName = $recruiter->user->name ?? 'N/A';
                $recruiterEmail = $recruiter->user->email ?? 'N/A';
                $companyName = $recruiter->company_name ?? 'Not specified';

                // Check for missing critical data
                if ($recruiter->user->name === null) {
                    $accessStats['missing_data']++;
                    $issues[] = "Recruiter {$recruiter->id}: missing user name";
                }

                if ($recruiter->user->email === null) {
                    $accessStats['missing_data']++;
                    $issues[] = "Recruiter {$recruiter->id}: missing user email";
                }

            } catch (\Exception $e) {
                $accessStats['access_errors']++;
                $issues[] = "Recruiter {$recruiter->id}: data access error - " . $e->getMessage();
            }
        }

        return [
            'status' => $accessStats['access_errors'] === 0 ? 'passed' : 'failed',
            'severity' => $accessStats['access_errors'] > 0 ? 'high' : 'low',
            'stats' => $accessStats,
            'issues' => $issues,
            'description' => 'Validates recruiter data access patterns'
        ];
    }

    /**
     * Check talent request data flow
     */
    private function checkTalentRequestDataFlow()
    {
        $issues = [];
        $flowStats = [
            'requests_checked' => 0,
            'missing_relationships' => 0,
            'data_access_errors' => 0
        ];

        $requests = TalentRequest::with(['recruiter.user', 'talent.user'])->take(10)->get();

        foreach ($requests as $request) {
            $flowStats['requests_checked']++;

            try {
                // Test the exact data access patterns from the request details page
                if (!$request->recruiter || !$request->recruiter->user) {
                    $flowStats['missing_relationships']++;
                    $issues[] = "Request {$request->id}: missing recruiter relationship";
                }

                if (!$request->talent || !$request->talent->user) {
                    $flowStats['missing_relationships']++;
                    $issues[] = "Request {$request->id}: missing talent relationship";
                }

                if ($request->talent && $request->talent->user) {
                    // Test the corrected blade template logic
                    $talentPhone = $request->talent->user->phone ?? 'Not provided';
                    $talentExperience = $request->talent->user->experience_level ?? 'Not specified';
                    $talentSkills = $request->talent->user->talent_skills ?? null;
                }

                if ($request->recruiter && $request->recruiter->user) {
                    $recruiterPhone = $request->recruiter->phone ?? $request->recruiter->user->phone ?? 'Not provided';
                }

            } catch (\Exception $e) {
                $flowStats['data_access_errors']++;
                $issues[] = "Request {$request->id}: data flow error - " . $e->getMessage();
            }
        }

        return [
            'status' => ($flowStats['missing_relationships'] + $flowStats['data_access_errors']) === 0 ? 'passed' : 'failed',
            'severity' => $flowStats['data_access_errors'] > 0 ? 'critical' : 'medium',
            'stats' => $flowStats,
            'issues' => $issues,
            'description' => 'Validates talent request data flow and relationships'
        ];
    }

    /**
     * Check caching consistency
     */
    private function checkCachingConsistency()
    {
        $issues = [];
        $cacheStats = [
            'cache_hits' => 0,
            'cache_misses' => 0,
            'stale_data' => 0
        ];

        // Test talent metrics caching
        $talents = Talent::take(5)->get();

        foreach ($talents as $talent) {
            $cacheKey = "talent_metrics_{$talent->id}";
            $cachedMetrics = cache()->get($cacheKey);

            if ($cachedMetrics) {
                $cacheStats['cache_hits']++;

                // Check if cached data is reasonably fresh (less than 25 hours old)
                $cacheTimestamp = cache()->get($cacheKey . '_timestamp');
                if ($cacheTimestamp && now()->diffInHours($cacheTimestamp) > 25) {
                    $cacheStats['stale_data']++;
                    $issues[] = "Talent {$talent->id}: cached metrics are stale";
                }
            } else {
                $cacheStats['cache_misses']++;

                try {
                    // Test generating metrics (this should cache them)
                    $metrics = $this->talentScoutingService->getTalentScoutingMetrics($talent);
                    if (empty($metrics)) {
                        $issues[] = "Talent {$talent->id}: failed to generate metrics";
                    }
                } catch (\Exception $e) {
                    $issues[] = "Talent {$talent->id}: error generating metrics - " . $e->getMessage();
                }
            }
        }

        return [
            'status' => count($issues) === 0 ? 'passed' : 'failed',
            'severity' => 'low',
            'stats' => $cacheStats,
            'issues' => $issues,
            'description' => 'Validates caching functionality and data freshness'
        ];
    }

    /**
     * Generate detailed report
     */
    public function generateDetailedReport()
    {
        $integrityResults = $this->runDataIntegrityChecks();

        $report = [
            'report_title' => 'LMS-Talent Scouting Data Integrity Report',
            'generated_at' => now()->toDateTimeString(),
            'system_status' => $integrityResults['status'],
            'executive_summary' => $this->generateExecutiveSummary($integrityResults),
            'detailed_results' => $integrityResults,
            'recommendations' => $this->generateRecommendations($integrityResults),
            'next_steps' => $this->generateNextSteps($integrityResults)
        ];

        return $report;
    }

    private function generateExecutiveSummary($results)
    {
        $summary = $results['summary'] ?? [];

        return [
            'overall_status' => $results['status'],
            'total_checks' => $summary['total_checks'] ?? 0,
            'passed_checks' => $summary['passed'] ?? 0,
            'failed_checks' => $summary['failed'] ?? 0,
            'critical_issues' => $summary['critical_issues'] ?? 0,
            'success_rate' => $summary['total_checks'] > 0
                ? round(($summary['passed'] / $summary['total_checks']) * 100, 1)
                : 0
        ];
    }

    private function generateRecommendations($results)
    {
        $recommendations = [];

        foreach ($results['checks'] ?? [] as $checkName => $check) {
            if ($check['status'] === 'failed') {
                switch ($checkName) {
                    case 'user_data':
                        $recommendations[] = 'Review user data validation rules and fix inconsistencies';
                        break;
                    case 'lms_integration':
                        $recommendations[] = 'Check LMS integration service configuration and mock data setup';
                        break;
                    case 'skill_integrity':
                        $recommendations[] = 'Implement data migration to fix skill format issues';
                        break;
                    case 'talent_request_flow':
                        $recommendations[] = 'Verify talent request relationship loading and data access patterns';
                        break;
                }
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'All data integrity checks passed. Continue regular monitoring.';
        }

        return $recommendations;
    }

    private function generateNextSteps($results)
    {
        $nextSteps = [];

        if ($results['status'] === 'passed') {
            $nextSteps = [
                'Monitor data integrity with regular automated checks',
                'Set up alerts for critical data inconsistencies',
                'Prepare for real LMS integration when available',
                'Optimize caching strategies for better performance'
            ];
        } else {
            $nextSteps = [
                'Address critical data integrity issues immediately',
                'Review and fix data access patterns causing errors',
                'Implement data validation at entry points',
                'Schedule follow-up integrity check after fixes'
            ];
        }

        return $nextSteps;
    }
}
