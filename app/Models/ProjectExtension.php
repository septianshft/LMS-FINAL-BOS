<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'requester_type',
        'requester_id',
        'old_end_date',
        'new_end_date',
        'justification',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes'
    ];

    protected $casts = [
        'old_end_date' => 'date',
        'new_end_date' => 'date',
        'reviewed_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const REQUESTER_RECRUITER = 'recruiter';
    const REQUESTER_TALENT = 'talent';

    /**
     * Get the project that this extension belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the requester (polymorphic relationship)
     */
    public function requester()
    {
        return $this->morphTo();
    }

    /**
     * Get the reviewer (talent admin)
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(TalentAdmin::class, 'reviewed_by');
    }

    /**
     * Check if extension is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if extension is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if extension is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Calculate extension duration in days
     */
    public function getExtensionDaysAttribute(): int
    {
        return $this->old_end_date->diffInDays($this->new_end_date);
    }

    /**
     * Scope to get pending extensions
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get approved extensions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get extensions for a specific project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
