<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assignment_id',
        'conflict_type',
        'severity',
        'detected_at',
        'project_end_date',
        'talent_proposed_end_date',
        'overrun_days',
        'overrun_percentage',
        'resolution_status',
        'resolution_strategy',
        'resolution_applied_at',
        'resolution_notes',
        'affected_talents_count',
        'budget_impact',
        'timeline_impact_days',
        'business_risk_level',
        'resolved_by',
        'admin_approved',
        'admin_approved_by',
        'admin_approved_at',
        'auto_resolved'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'project_end_date' => 'date',
        'talent_proposed_end_date' => 'date',
        'resolution_applied_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'overrun_percentage' => 'decimal:2',
        'budget_impact' => 'decimal:2',
        'admin_approved' => 'boolean',
        'auto_resolved' => 'boolean'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ProjectAssignment::class, 'assignment_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function adminApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    // Helper methods
    public function isResolved(): bool
    {
        return $this->resolution_status === 'resolved';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function requiresApproval(): bool
    {
        return !$this->admin_approved && in_array($this->severity, ['high', 'critical']);
    }

    // Scopes
    public function scopeUnresolved($query)
    {
        return $query->whereIn('resolution_status', ['detected', 'analyzing']);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolution_status', 'resolved');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeByProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
