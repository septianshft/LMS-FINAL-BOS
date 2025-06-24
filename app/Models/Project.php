<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_admin';
    const STATUS_APPROVED = 'approved';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_EXTENSION_REQUESTED = 'extension_requested';
    const STATUS_CLOSURE_REQUESTED = 'closure_requested';

    protected $fillable = [
        'title',
        'description',
        'industry',
        'general_requirements',
        'overall_budget_min',
        'overall_budget_max',
        'expected_start_date',
        'expected_end_date',
        'estimated_duration_days',
        'status',
        'recruiter_id',
        'admin_approved_by',
        'admin_approved_at',
        'admin_notes',
        'requested_end_date',
        'extension_reason',
        'additional_budget',
        'extension_requested_at',
        'days_overdue',
        'overdue_since',
        'auto_extended',
        'grace_period_used',
        'closure_requested_at',
        'closure_requested_by',
        'closure_reason',
        'closure_approved_at',
        'closure_approved_by'
    ];

    protected $casts = [
        'expected_start_date' => 'date',
        'expected_end_date' => 'date',
        'requested_end_date' => 'date',
        'overdue_since' => 'date',
        'admin_approved_at' => 'datetime',
        'extension_requested_at' => 'datetime',
        'closure_requested_at' => 'datetime',
        'closure_approved_at' => 'datetime',
        'overall_budget_min' => 'decimal:2',
        'overall_budget_max' => 'decimal:2',
        'additional_budget' => 'decimal:2',
        'auto_extended' => 'boolean',
        'grace_period_used' => 'boolean'
    ];

    // Relationships
    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(Recruiter::class);
    }

    public function adminApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    public function closureRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closure_requested_by');
    }

    public function closureApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closure_approved_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ProjectTimelineEvent::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(ProjectExtension::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(TimelineConflict::class);
    }

    public function talentRequests(): HasMany
    {
        return $this->hasMany(TalentRequest::class);
    }

    // Helper methods
    public function getRemainingDaysAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays($this->expected_end_date, false));
    }

    public function isOverdue(): bool
    {
        return $this->expected_end_date < Carbon::now() && in_array($this->status, ['active', 'overdue']);
    }

    public function isNearingEnd(int $days = 30): bool
    {
        return $this->remaining_days <= $days && $this->remaining_days > 0;
    }

    public function canAddTalent(): bool
    {
        return $this->remaining_days >= 7 && in_array($this->status, ['approved', 'active']);
    }

    public function requiresExtensionForTalent(int $talentDurationDays): bool
    {
        return $this->remaining_days < 30 && $talentDurationDays > ($this->remaining_days + 30);
    }

    /**
     * Get project progress percentage based on timeline
     */
    public function getProgressPercentage()
    {
        if (!$this->expected_start_date || !$this->expected_end_date) {
            return 0;
        }

        $totalDays = $this->expected_start_date->diffInDays($this->expected_end_date);
        $daysPassed = $this->expected_start_date->diffInDays(now());

        if ($daysPassed <= 0) {
            return 0;
        }

        if ($daysPassed >= $totalDays) {
            return 100;
        }

        return round(($daysPassed / $totalDays) * 100);
    }

    /**
     * Check if project can request extension
     */
    public function canRequestExtension()
    {
        // Can only request extension if project is active and no pending extensions
        return $this->status === 'active' &&
               !$this->extensions()->where('status', 'pending')->exists();
    }

    /**
     * Get pending extensions
     */
    public function pendingExtensions()
    {
        return $this->hasMany(ProjectExtension::class)->where('status', 'pending');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'active')
                  ->where('expected_end_date', '<', Carbon::now());
            });
    }

    public function scopeNearingEnd($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->whereBetween('expected_end_date', [Carbon::now(), Carbon::now()->addDays($days)]);
    }

    public function scopeByRecruiter($query, int $recruiterId)
    {
        return $query->where('recruiter_id', $recruiterId);
    }
}
