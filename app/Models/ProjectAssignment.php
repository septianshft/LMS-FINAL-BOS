<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProjectAssignment extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'project_id',
        'talent_id',
        'specific_role',
        'talent_start_date',
        'talent_end_date',
        'individual_budget',
        'specific_requirements',
        'working_hours_per_week',
        'priority_level',
        'assignment_notes',
        'status',
        'admin_approved_at',
        'admin_approved_by',
        'talent_accepted_at',
        'talent_rejected_at',
        'rejection_reason',
        'actual_start_date',
        'completed_at',
        'completion_rating'
    ];

    protected $casts = [
        'talent_start_date' => 'date',
        'talent_end_date' => 'date',
        'original_end_date' => 'date',
        'individual_budget' => 'decimal:2',
        'admin_approved_at' => 'datetime',
        'talent_accepted_at' => 'datetime',
        'talent_rejected_at' => 'datetime',
        'last_timeline_modification' => 'datetime',
        'completed_at' => 'datetime',
        'timeline_conflict_detected' => 'boolean',
        'auto_resolution_applied' => 'boolean'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function talent(): BelongsTo
    {
        return $this->belongsTo(Talent::class);
    }

    public function adminApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    public function continuationProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'continuation_project_id');
    }

    // Helper methods
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['assigned', 'admin_pending', 'talent_pending']);
    }

    public function getDurationInDays(): int
    {
        return $this->talent_start_date->diffInDays($this->talent_end_date);
    }

    public function getRemainingDays(): int
    {
        return max(0, Carbon::now()->diffInDays($this->talent_end_date, false));
    }

    public function isOverdue(): bool
    {
        return $this->talent_end_date < Carbon::now() && $this->isActive();
    }

    public function hasTimelineConflict(): bool
    {
        return $this->timeline_conflict_detected;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['assigned', 'admin_pending', 'talent_pending']);
    }

    public function scopeByTalent($query, int $talentId)
    {
        return $query->where('talent_id', $talentId);
    }

    public function scopeByProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
