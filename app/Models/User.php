<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'pekerjaan',
        'email',
        'password',
        'last_login_at',
        // Talent scouting fields
        'available_for_scouting',
        'talent_skills',
        'talent_bio',
        'portfolio_url',
        'location',
        'phone',
        'experience_level',
        'is_active_talent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'talent_skills' => 'array',
            'available_for_scouting' => 'boolean',
            'is_active_talent' => 'boolean',
        ];
    }

    /**
     * Get the avatar URL with fallback to default
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }

        // Fallback to default avatar
        return asset('images/default-avatar.svg');
    }

    /**
     * Get the avatar path for storage with fallback
     */
    public function getAvatarPathAttribute(): string
    {
        return $this->avatar ?: 'images/default-avatar.svg';
    }

    public function courses(){
        return $this->belongsToMany(Course::class, 'course_trainees');
    }

    public function subscribe_transaction(){
        return $this->hasMany(SubscribeTransaction::class);
    }

    public function hasActiveSubscription(?Course $course = null)
    {
        $query = SubscribeTransaction::where('user_id', $this->id)
            ->where('is_paid', true);

        if ($course) {
            $query->where('course_id', $course->id); // hanya boleh akses kelas yang dibayarkan
        }

        return $query->exists();
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    public function recruiter()
    {
        return $this->hasOne(Recruiter::class);
    }

    public function talentAdmin()
    {
        return $this->hasOne(TalentAdmin::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // TALENT SCOUTING INTEGRATION METHODS

    /**
     * Standardized accessor for talent_skills field.
     * Always returns an array, handles JSON string, array, or null.
     */
    public function getTalentSkillsArray(): array
    {
        $talents_skills = $this->talent_skills;
        // Handle case where talent_skills might be a string (legacy data)
        if (is_string($talents_skills)) {
            $talents_skills = json_decode($talents_skills, true) ?? [];
        }
        // Ensure we have an array
        return is_array($talents_skills) ? $talents_skills : [];
    }
    /**
     * Add skill from course completion - simplified approach
     */
    public function addSkillFromCourse($course)
    {
        $existingSkills = $this->getTalentSkillsArray();

        // Simplified: Use course name directly as skill name
        $skillName = $course->name;

        // Simplified: Use course level directly as proficiency
        $proficiency = $course->level ? strtolower($course->level->name) : 'intermediate';

        // Check if skill already exists
        $existingSkillIndex = collect($existingSkills)->search(function ($skill) use ($skillName) {
            return $skill['skill_name'] === $skillName;
        });

        if ($existingSkillIndex !== false) {
            // Update existing skill with higher proficiency if applicable
            $existingProficiency = $existingSkills[$existingSkillIndex]['proficiency'];
            if ($this->getSkillLevelNumber($proficiency) > $this->getSkillLevelNumber($existingProficiency)) {
                $existingSkills[$existingSkillIndex]['proficiency'] = $proficiency;
                $existingSkills[$existingSkillIndex]['completed_date'] = now()->toDateString();
            }
        } else {
            // Add new skill with simplified structure
            $existingSkills[] = [
                'skill_name' => $skillName,
                'proficiency' => $proficiency,
                'course_id' => $course->id,
                'completed_date' => now()->toDateString(),
            ];
        }

        $this->update(['talent_skills' => $existingSkills]);

        // Trigger conversion suggestion if conditions are met
        $this->checkConversionSuggestion();
    }

    /**
     * Get skill level as number for comparison
     */
    private function getSkillLevelNumber($level)
    {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3];
        return $levels[strtolower($level)] ?? 2;
    }

    /**
     * Check if user should be suggested for talent conversion - simplified
     */
    private function checkConversionSuggestion()
    {
        if ($this->available_for_scouting) {
            return; // Already opted in
        }

        $skillCount = count($this->getTalentSkillsArray());
        $courseCount = $this->completedCourses()->count();

        // Simplified conversion criteria
        $shouldSuggest = $skillCount >= 3 || $courseCount >= 5;

        if ($shouldSuggest) {
            session()->flash('smart_talent_suggestion', [
                'message' => 'Great progress! You\'ve gained valuable skills. Consider joining our talent platform to connect with recruiters.',
                'action_url' => route('profile.edit') . '#talent-settings',
                'skill_count' => $skillCount,
                'reason' => $this->getConversionReason($skillCount, $courseCount)
            ]);
        }
    }

    /**
     * Get personalized conversion reason - simplified
     */
    private function getConversionReason($skillCount, $courseCount)
    {
        if ($skillCount >= 5) {
            return 'You have ' . $skillCount . ' verified skills - perfect for attracting recruiters!';
        }
        if ($courseCount >= 5) {
            return 'You\'ve completed ' . $courseCount . ' courses - show your dedication to employers!';
        }
        return 'Your learning progress is impressive - time to monetize your skills!';
    }

    /**
     * Get completed courses relationship
     */
    public function completedCourses()
    {
        return $this->belongsToMany(Course::class, 'course_trainees')
                   ->whereExists(function($query) {
                       $query->select(DB::raw(1))
                             ->from('course_progresses')
                             ->whereColumn('course_progresses.course_id', 'courses.id')
                             ->where('course_progresses.user_id', $this->id)
                             ->where('course_progresses.progress', 100);
                   });
    }

    /**
     * Get course progress relationship
     */
    public function courseProgress()
    {
        return $this->hasMany(CourseProgress::class);
    }

    /**
     * Enable talent scouting with enhanced onboarding
     */
    public function enableTalentScouting($additionalData = [])
    {
        $updateData = array_merge([
            'available_for_scouting' => true,
            'is_active_talent' => true,
        ], $additionalData);

        $this->update($updateData);

        // Assign talent role if not already assigned
        if (!$this->hasRole('talent')) {
            $this->assignRole('talent');
        }

        // Create or update talent record
        if (!$this->talent) {
            $this->talent()->create(['is_active' => true]);
        } else {
            $this->talent->update(['is_active' => true]);
        }
    }

    /**
     * Disable talent scouting
     */
    public function disableTalentScouting()
    {
        $this->update([
            'available_for_scouting' => false,
            'is_active_talent' => false,
        ]);

        if ($this->talent) {
            $this->talent->update(['is_active' => false]);
        }
    }

    /**
     * Check if user is available for talent scouting
     */
    public function isAvailableForScouting()
    {
        return $this->available_for_scouting && $this->is_active_talent;
    }

    /**
     * Get talent relationship
     */
    public function talent()
    {
        return $this->hasOne(Talent::class);
    }

    /**
     * Get skills organized by proficiency level - simplified
     */
    public function getSkillsByProficiency()
    {
        $skills = $this->getTalentSkillsArray();
        $organized = [];

        foreach ($skills as $skill) {
            // Ensure skill is an array
            if (!is_array($skill)) {
                continue;
            }

            $proficiency = $skill['proficiency'] ?? 'intermediate';
            if (!isset($organized[$proficiency])) {
                $organized[$proficiency] = [];
            }
            $organized[$proficiency][] = $skill;
        }

        return $organized;
    }

    /**
     * Calculate talent readiness score - simplified
     */
    public function getTalentReadinessScore()
    {
        $score = 0;
        $skillCount = count($this->getTalentSkillsArray());
        $completedCourses = $this->completedCourses()->count();

        // Skills contribute 50% of score
        $score += min(($skillCount * 10), 50);

        // Course completions contribute 40% of score
        $score += min(($completedCourses * 8), 40);

        // Recent activity contributes 10% of score
        $recentSkills = array_filter($this->getTalentSkillsArray(), function($skill) {
            if (!is_array($skill)) return false;
            $completedDate = \Carbon\Carbon::parse($skill['completed_date'] ?? now());
            return $completedDate->gte(\Carbon\Carbon::now()->subMonths(3));
        });
        $score += min((count($recentSkills) * 5), 10);

        return min($score, 100);
    }

    /**
     * Get learning velocity (skills per month)
     */
    public function getLearningVelocity()
    {
        $skills = $this->getTalentSkillsArray();
        if (count($skills) < 2) return 0;

        $dates = array_map(function($skill) {
            if (!is_array($skill)) return \Carbon\Carbon::now();
            return \Carbon\Carbon::parse($skill['completed_date'] ?? $skill['acquired_at'] ?? now());
        }, $skills);

        sort($dates);
        $monthsDiff = $dates[0]->diffInMonths(end($dates));
        if ($monthsDiff == 0) $monthsDiff = 1;

        return round(count($skills) / $monthsDiff, 2);
    }

    /**
     * Get skill progress analytics - simplified
     */
    public function getSkillAnalytics()
    {
        $skills = $this->getTalentSkillsArray();
        $proficiencies = collect($skills)->groupBy('proficiency');

        // Create skill_levels array with default values
        $skillLevels = [
            'beginner' => 0,
            'intermediate' => 0,
            'advanced' => 0
        ];

        // Fill in actual counts
        foreach ($proficiencies as $proficiency => $skillsGroup) {
            $skillLevels[$proficiency] = $skillsGroup->count();
        }

        return [
            'total_skills' => count($skills),
            'skill_levels' => $skillLevels,
            'recent_skills' => collect($skills)->where('completed_date', '>=', now()->subDays(30)->toDateString())->count()
        ];
    }

    /**
     * Get user's primary skill proficiency for analytics - simplified
     */
    public function getPrimarySkillProficiency(): string
    {
        $skills = $this->getTalentSkillsArray();
        if (empty($skills)) {
            return 'beginner';
        }

        // Count proficiency levels
        $proficiencies = [];
        foreach ($skills as $skill) {
            if (!is_array($skill)) {
                continue;
            }
            $proficiency = $skill['proficiency'] ?? 'intermediate';
            $proficiencies[$proficiency] = ($proficiencies[$proficiency] ?? 0) + 1;
        }

        // Return the most common proficiency level
        arsort($proficiencies);
        return array_keys($proficiencies)[0] ?? 'intermediate';
    }

    /**
     * Calculate readiness score for talent conversion
     * Phase 1 Enhancement Method
     */
    public function calculateReadinessScore(): float
    {
        $score = 0;

        // Course completion factor (40% weight)
        $completedCourses = $this->courseProgress()->where('progress', 100)->count();
        $totalCourses = $this->courseProgress()->count();
        if ($totalCourses > 0) {
            $completionRate = $completedCourses / $totalCourses;
            $score += $completionRate * 40;
        }

        // Quiz performance factor (30% weight)
        $quizAverage = $this->getQuizAverage();
        $score += ($quizAverage / 100) * 30;

        // Skills factor (20% weight)
        $skillCount = count($this->getTalentSkillsArray());
        $score += min($skillCount * 4, 20); // Cap at 20 points

        // Recent activity factor (10% weight)
        $recentActivity = $this->courseProgress()
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        $score += min($recentActivity * 2, 10); // Cap at 10 points

        return round($score, 2);
    }

    /**
     * Get user's average quiz score
     * Phase 1 Enhancement Method
     */
    private function getQuizAverage(): float
    {
        $quizAttempts = $this->quizAttempts()
            ->where('is_passed', true)
            ->get();

        if ($quizAttempts->isEmpty()) {
            return 0;
        }

        $totalScore = $quizAttempts->sum('score');
        return round($totalScore / $quizAttempts->count(), 2);
    }

    /**
     * Get quiz attempts relationship
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // PHASE 1: ENHANCED ANALYTICS COMPATIBILITY METHODS

    /**
     * Get conversion readiness score (alias for calculateReadinessScore)
     */
    public function getConversionReadinessScore(): float
    {
        return $this->calculateReadinessScore();
    }

    /**
     * Get skill proficiencies for analytics - simplified
     */
    public function getSkillProficiencies(): array
    {
        $skills = $this->getTalentSkillsArray();
        $proficiencies = [];

        foreach ($skills as $skill) {
            if (!is_array($skill)) continue;
            $proficiency = $skill['proficiency'] ?? 'intermediate';
            if (!in_array($proficiency, $proficiencies)) {
                $proficiencies[] = $proficiency;
            }
        }

        return $proficiencies;
    }


    /**
     * Get conversion suggestion status
     */
    public function shouldSuggestTalentConversion(): bool
    {
        // Don't suggest if already a talent
        if ($this->hasRole('talent')) {
            return false;
        }

        // Don't suggest if available for scouting
        if ($this->available_for_scouting) {
            return false;
        }

        // Suggest if high readiness score and multiple completed courses
        $readinessScore = $this->calculateReadinessScore();
        $completedCourses = $this->courseProgress()->where('progress', 100)->count();

        return $readinessScore >= 70 && $completedCourses >= 2;
    }

    /**
     * Get readiness level label
     */
    public function getReadinessLevel(): string
    {
        $score = $this->calculateReadinessScore();

        if ($score >= 85) return 'Excellent';
        if ($score >= 70) return 'High';
        if ($score >= 55) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Very Low';
    }

    /**
     * Get talent conversion metrics for analytics - simplified
     */
    public function getConversionMetrics(): array
    {
        return [
            'readiness_score' => $this->calculateReadinessScore(),
            'readiness_level' => $this->getReadinessLevel(),
            'completed_courses' => $this->courseProgress()->where('progress', 100)->count(),
            'skill_count' => count($this->getTalentSkillsArray()),
            'skill_proficiencies' => $this->getSkillProficiencies(),
            'quiz_average' => $this->getQuizAverage(),
            'learning_velocity' => $this->getLearningVelocity(),
            'conversion_suggested' => $this->shouldSuggestTalentConversion(),
            'is_talent' => $this->hasRole('talent'),
            'available_for_scouting' => $this->available_for_scouting
        ];
    }
}
