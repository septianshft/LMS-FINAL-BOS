<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTimelineEvent extends Model
{
    use HasFactory;    protected $fillable = [
        'project_id',
        'event_type',
        'description',
        'user_id',
        'event_data'
    ];

    protected $casts = [
        'event_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];    const EVENT_CREATED = 'created';
    const EVENT_APPROVED = 'approved';
    const EVENT_REJECTED = 'rejected';
    const EVENT_CANCELLED = 'cancelled';
    const EVENT_STARTED = 'started';
    const EVENT_TALENT_ASSIGNED = 'talent_assigned';
    const EVENT_TALENT_ACCEPTED = 'talent_accepted';
    const EVENT_TALENT_DECLINED = 'talent_rejected';
    const EVENT_EXTENSION_REQUESTED = 'extension_requested';
    const EVENT_EXTENSION_APPROVED = 'extension_approved';
    const EVENT_EXTENSION_REJECTED = 'extension_rejected';
    const EVENT_EXTENDED = 'extended';
    const EVENT_OVERDUE = 'overdue';
    const EVENT_CLOSURE_REQUESTED = 'closure_requested';
    const EVENT_COMPLETED = 'completed';
    const EVENT_CONFLICT_DETECTED = 'conflict_detected';
    const EVENT_CONFLICT_RESOLVED = 'conflict_resolved';
    const EVENT_NOTIFICATION_SENT = 'notification_sent';

    /**
     * Get the project that this timeline event belongs to
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }    /**
     * Get the user that triggered this event
     */
    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get events for a specific project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to get events by type
     */
    public function scopeByType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope to get events within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_date', [$startDate, $endDate]);
    }    /**
     * Check if this is a critical event (deadline related)
     */
    public function isCritical(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_OVERDUE,
            self::EVENT_CONFLICT_DETECTED
        ]);
    }    /**
     * Check if this is a milestone event
     */
    public function isMilestone(): bool
    {
        return in_array($this->event_type, [
            self::EVENT_CREATED,
            self::EVENT_APPROVED,
            self::EVENT_COMPLETED
        ]);
    }    /**
     * Get formatted event description with metadata
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $description = $this->description;

        if ($this->event_data && is_array($this->event_data)) {
            foreach ($this->event_data as $key => $value) {
                $description = str_replace("{{$key}}", $value, $description);
            }
        }

        return $description;
    }    /**
     * Create a timeline event
     */
    public static function createEvent(
        int $projectId,
        string $eventType,
        string $description,
        $triggeredBy = null,
        array $eventData = []
    ): self {
        return self::create([
            'project_id' => $projectId,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => $triggeredBy ? $triggeredBy->user_id ?? $triggeredBy->id : null,
            'event_data' => $eventData
        ]);
    }
}
