<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talents';

    protected $fillable = [
        'user_id',
        'is_active',
        'scouting_metrics',
        'redflagged',
        'redflag_reason',
    ];

    protected $casts = [
        'scouting_metrics' => 'array',
        'is_active' => 'boolean',
        'redflagged' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function talentRequests(){
        return $this->hasMany(TalentRequest::class);
    }

    public function assignments(){
        return $this->hasMany(ProjectAssignment::class);
    }

    /**
     * Get completed talent requests that have been red-flagged
     */
    public function getRedflaggedCompletedRequests()
    {
        return $this->talentRequests()
            ->where('status', 'completed')
            ->where('is_redflagged', true)
            ->with('redflaggedBy', 'recruiter.user')
            ->get();
    }

    /**
     * Get total count of red-flagged completed projects
     */
    public function getRedflagCount()
    {
        return $this->talentRequests()
            ->where('status', 'completed')
            ->where('is_redflagged', true)
            ->count();
    }

    /**
     * Get total count of completed projects
     */
    public function getCompletedProjectCount()
    {
        return $this->talentRequests()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get redflag rate percentage
     */
    public function getRedflagRate()
    {
        $completed = $this->getCompletedProjectCount();
        if ($completed === 0) {
            return 0;
        }

        $redflagged = $this->getRedflagCount();
        return round(($redflagged / $completed) * 100, 1);
    }

    /**
     * Check if talent has any red flags
     */
    public function hasRedflags()
    {
        return $this->getRedflagCount() > 0;
    }

    /**
     * Get redflag summary for display
     */
    public function getRedflagSummary()
    {
        $redflagCount = $this->getRedflagCount();
        $completedCount = $this->getCompletedProjectCount();

        if ($redflagCount === 0) {
            return [
                'has_redflags' => false,
                'count' => 0,
                'total_completed' => $completedCount,
                'rate' => 0,
                'display_text' => 'No red flags',
                'badge_class' => 'bg-green-100 text-green-800'
            ];
        }

        return [
            'has_redflags' => true,
            'count' => $redflagCount,
            'total_completed' => $completedCount,
            'rate' => $this->getRedflagRate(),
            'display_text' => "{$redflagCount} of {$completedCount} projects flagged",
            'badge_class' => 'bg-red-100 text-red-800'
        ];
    }
}
