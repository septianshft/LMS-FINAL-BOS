<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class TalentRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talent_requests';

    protected $fillable = [
        'recruiter_id',
        'talent_id',
        'talent_user_id', // New field for direct user reference
        'project_title',
        'project_description',
        'requirements',
        'budget_range',
        'project_duration',
        'status',
        'approved_at',
        'meeting_arranged_at',
        'onboarded_at',
        // New dual acceptance fields
        'talent_accepted',
        'talent_accepted_at',
        'admin_accepted',
        'admin_accepted_at',
        'both_parties_accepted',
        'workflow_completed_at',
        // Time-blocking fields
        'project_start_date',
        'project_end_date',
        'is_blocking_talent',
        'blocking_notes',
        // Project-centric system fields
        'project_id',
        'migrated_to_project_system',
        'legacy_talent_request_id',
        // Per-request redflag fields
        'is_redflagged',
        'redflag_reason',
        'redflagged_at',
        'redflagged_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'meeting_arranged_at' => 'datetime',
        'onboarded_at' => 'datetime',
        'talent_accepted_at' => 'datetime',
        'admin_accepted_at' => 'datetime',
        'workflow_completed_at' => 'datetime',
        'talent_accepted' => 'boolean',
        'admin_accepted' => 'boolean',
        'both_parties_accepted' => 'boolean',
        // Time-blocking casts
        'project_start_date' => 'datetime',
        'project_end_date' => 'datetime',
        'is_blocking_talent' => 'boolean',
        'deleted_at' => 'datetime',
        // Project-centric system casts
        'migrated_to_project_system' => 'boolean',
        // Per-request redflag casts
        'is_redflagged' => 'boolean',
        'redflagged_at' => 'datetime'
    ];

    // Relationships
    public function recruiter()
    {
        return $this->belongsTo(Recruiter::class);
    }

    public function talent()
    {
        return $this->belongsTo(Talent::class);
    }

    // New relationship to user directly
    public function talentUser()
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    // Alias for easier access in views and controllers
    public function user()
    {
        return $this->belongsTo(User::class, 'talent_user_id');
    }

    /**
     * Relationship to the new project system
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship to the admin who red-flagged this request
     */
    public function redflaggedBy()
    {
        return $this->belongsTo(User::class, 'redflagged_by');
    }

    // Helper method to get talent user (either from direct reference or through talent)
    public function getTalentUser()
    {
        return $this->talentUser ?? $this->talent?->user;
    }

    // ===================================================
    // CENTRALIZED STATUS CONFIGURATION
    // ===================================================

    /**
     * Get centralized status configuration for consistent handling across views
     */
    public static function getStatusConfig()
    {
        return [
            'pending' => [
                'label' => 'Pending Review',
                'color' => 'warning',
                'bootstrap_class' => 'bg-warning text-dark',
                'tailwind_class' => 'bg-yellow-100 text-yellow-800',
                'icon' => 'fas fa-clock',
                'translation' => 'Tertunda'
            ],
            'approved' => [
                'label' => 'Approved by Admin',
                'color' => 'info',
                'bootstrap_class' => 'bg-info',
                'tailwind_class' => 'bg-blue-100 text-blue-800',
                'icon' => 'fas fa-check',
                'translation' => 'Disetujui'
            ],
            'meeting_arranged' => [
                'label' => 'Meeting Arranged',
                'color' => 'primary',
                'bootstrap_class' => 'bg-primary',
                'tailwind_class' => 'bg-indigo-100 text-indigo-800',
                'icon' => 'fas fa-calendar',
                'translation' => 'Pertemuan Diatur'
            ],
            'agreement_reached' => [
                'label' => 'Agreement Reached',
                'color' => 'success',
                'bootstrap_class' => 'bg-success',
                'tailwind_class' => 'bg-green-100 text-green-800',
                'icon' => 'fas fa-handshake',
                'translation' => 'Kesepakatan Tercapai'
            ],
            'onboarded' => [
                'label' => 'Talent Onboarded',
                'color' => 'success',
                'bootstrap_class' => 'bg-success',
                'tailwind_class' => 'bg-green-100 text-green-800',
                'icon' => 'fas fa-user-plus',
                'translation' => 'Bergabung'
            ],
            'rejected' => [
                'label' => 'Request Rejected',
                'color' => 'danger',
                'bootstrap_class' => 'bg-danger',
                'tailwind_class' => 'bg-red-100 text-red-800',
                'icon' => 'fas fa-times',
                'translation' => 'Ditolak'
            ],
            'completed' => [
                'label' => 'Project Completed',
                'color' => 'secondary',
                'bootstrap_class' => 'bg-secondary',
                'tailwind_class' => 'bg-gray-100 text-gray-800',
                'icon' => 'fas fa-flag-checkered',
                'translation' => 'Selesai'
            ]
        ];
    }

    /**
     * Get status configuration for current status
     */
    public function getStatusConfiguration()
    {
        $config = self::getStatusConfig();
        return $config[$this->status] ?? $config['pending'];
    }

    /**
     * Get unified Tailwind CSS classes for status badges
     */
    public function getStatusBadgeClasses()
    {
        return $this->getStatusConfiguration()['tailwind_class'];
    }

    /**
     * Get status icon
     */
    public function getStatusIcon()
    {
        return $this->getStatusConfiguration()['icon'];
    }

    /**
     * Get status translation (Indonesian)
     */
    public function getStatusTranslation()
    {
        return $this->getStatusConfiguration()['translation'];
    }

    // Status helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isMeetingArranged()
    {
        return $this->status === 'meeting_arranged';
    }

    public function isOnboarded()
    {
        return $this->status === 'onboarded';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Status badge color (Bootstrap classes)
    public function getStatusBadgeColor()
    {
        return $this->getStatusConfiguration()['color'];
    }

    // Bootstrap badge classes
    public function getBootstrapBadgeClasses()
    {
        return $this->getStatusConfiguration()['bootstrap_class'];
    }

    // Formatted status
    public function getFormattedStatus()
    {
        return $this->getStatusConfiguration()['label'];
    }

    // Dual acceptance workflow methods
    public function isTalentAccepted()
    {
        return $this->talent_accepted;
    }

    public function isAdminAccepted()
    {
        return $this->admin_accepted;
    }

    public function areBothPartiesAccepted()
    {
        return $this->both_parties_accepted;
    }

    public function canProceedToMeeting()
    {
        return $this->talent_accepted && $this->admin_accepted && $this->both_parties_accepted;
    }

    /**
     * Check if the request can proceed to onboarding
     * Both talent and admin must have accepted
     */
    public function canProceedToOnboarding()
    {
        return $this->talent_accepted && $this->admin_accepted && $this->status !== 'rejected';
    }

    public function markTalentAccepted()
    {
        $updateData = [
            'talent_accepted' => true,
            'talent_accepted_at' => now(),
            // Update status to approved if still pending
            'status' => $this->status === 'pending' ? 'approved' : $this->status
        ];

        // If admin has already accepted, mark both accepted and auto-transition
        if ($this->admin_accepted) {
            $updateData['both_parties_accepted'] = true;
            $updateData['workflow_completed_at'] = now();
            // AUTO-TRANSITION: When both parties accept, move to meeting_arranged
            $updateData['status'] = 'meeting_arranged';
            $updateData['meeting_arranged_at'] = now();
        }

        $this->update($updateData);

        // Start time-blocking if both parties now accepted
        if ($this->admin_accepted) {
            $this->startTimeBlocking();
        }

        return $this;
    }

    public function markAdminAccepted()
    {
        $updateData = [
            'admin_accepted' => true,
            'admin_accepted_at' => now()
        ];

        // If talent has already accepted, mark both accepted and auto-transition
        if ($this->talent_accepted) {
            $updateData['both_parties_accepted'] = true;
            $updateData['workflow_completed_at'] = now();
            // AUTO-TRANSITION: When both parties accept, move to meeting_arranged
            $updateData['status'] = 'meeting_arranged';
            $updateData['meeting_arranged_at'] = now();
        }

        $this->update($updateData);

        // Start time-blocking if both parties now accepted
        if ($this->talent_accepted) {
            $this->startTimeBlocking();
        }

        return $this;
    }

    public function checkAndMarkBothAccepted()
    {
        // STRICT VALIDATION: Both talent AND admin must explicitly accept
        if ($this->talent_accepted && $this->admin_accepted && !$this->both_parties_accepted) {
            // Double-check to prevent accidental approvals
            if ($this->talent_accepted_at && $this->admin_accepted_at) {
                $this->update([
                    'both_parties_accepted' => true,
                    'workflow_completed_at' => now(),
                    'status' => 'meeting_arranged' // Both parties accepted - ready for meeting
                ]);

                // Start time-blocking when both parties accept
                $this->startTimeBlocking();
            }
        }
    }

    public function getAcceptanceStatus()
    {
        if ($this->both_parties_accepted) {
            return 'Both parties accepted - Meeting can be arranged';
        } elseif ($this->talent_accepted && $this->admin_accepted) {
            return 'Both accepted - Processing';
        } elseif ($this->talent_accepted) {
            return 'Talent accepted - Waiting for admin';
        } elseif ($this->admin_accepted) {
            return 'Admin accepted - Waiting for talent';
        } else {
            return 'Pending acceptance from both parties';
        }
    }

    /**
     * Get talent-friendly acceptance status for better UX
     */
    public function getTalentFriendlyAcceptanceStatus()
    {
        if ($this->both_parties_accepted) {
            return 'Both parties accepted - Meeting can be arranged';
        } elseif ($this->talent_accepted && $this->admin_accepted) {
            return 'Both accepted - Processing';
        } elseif ($this->talent_accepted) {
            return 'You accepted - Waiting for admin approval';
        } elseif ($this->admin_accepted) {
            return 'Request pre-approved by admin - Click to accept';
        } else {
            return 'Pending your response';
        }
    }

    /**
     * Check if this request is pre-approved (admin accepted but talent hasn't)
     */
    public function isPreApproved()
    {
        return $this->admin_accepted && !$this->talent_accepted &&
               ($this->status === 'pending' || $this->status === 'approved');
    }

    public function getWorkflowProgress()
    {
        $progress = 0;

        if ($this->status !== 'rejected') {
            $progress += 20; // Request submitted
        }

        if ($this->talent_accepted) {
            $progress += 30; // Talent accepted
        }

        if ($this->admin_accepted) {
            $progress += 30; // Admin accepted
        }

        if ($this->both_parties_accepted) {
            $progress += 20; // Both accepted, ready for meeting
        }

        return min(100, $progress);
    }

    // ===================================================
    // TIME-BLOCKING SYSTEM METHODS
    // ===================================================

    /**
     * Check if this request is currently blocking the talent
     */
    public function isCurrentlyBlockingTalent(): bool
    {
        if (!$this->is_blocking_talent) {
            return false;
        }

        $now = now();
        return $this->project_start_date && $this->project_end_date &&
               $now->between($this->project_start_date, $this->project_end_date);
    }

    /**
     * Calculate project end date based on duration string
     */
    public function calculateProjectEndDate($startDate = null)
    {
        $startDate = $startDate ?: now();

        if (!$this->project_duration) {
            return null;
        }

        $duration = strtolower($this->project_duration);

        // Parse various duration formats
        if (preg_match('/(\d+)\s*(week|weeks)/', $duration, $matches)) {
            return $startDate->copy()->addWeeks($matches[1]);
        } elseif (preg_match('/(\d+)\s*(month|months)/', $duration, $matches)) {
            return $startDate->copy()->addMonths($matches[1]);
        } elseif (preg_match('/(\d+)\s*(day|days)/', $duration, $matches)) {
            return $startDate->copy()->addDays($matches[1]);
        } elseif (preg_match('/(\d+)-(\d+)\s*(month|months)/', $duration, $matches)) {
            // Handle ranges like "2-3 months" - use the maximum
            return $startDate->copy()->addMonths($matches[2]);
        } elseif (preg_match('/(\d+)-(\d+)\s*(week|weeks)/', $duration, $matches)) {
            return $startDate->copy()->addWeeks($matches[2]);
        }

        // Default mapping for common durations
        $durationMap = [
            '1-2 weeks' => ['weeks' => 2],
            '1 month' => ['months' => 1],
            '2-3 months' => ['months' => 3],
            '3-6 months' => ['months' => 6],
            '6+ months' => ['months' => 6],
            'ongoing' => ['months' => 12], // Default for ongoing projects
        ];

        if (isset($durationMap[$duration])) {
            $mapping = $durationMap[$duration];
            $date = $startDate->copy();

            if (isset($mapping['weeks'])) {
                $date->addWeeks((int)$mapping['weeks']);
            } elseif (isset($mapping['months'])) {
                $date->addMonths((int)$mapping['months']);
            }

            return $date;
        }

        // Default fallback: 3 months
        return $startDate->copy()->addMonths(3);
    }

    /**
     * Start time-blocking for this talent
     */
    public function startTimeBlocking($startDate = null): void
    {
        $startDate = $startDate ?: now();
        $endDate = $this->calculateProjectEndDate($startDate);

        // If end date calculation fails, use default 3 months
        if (!$endDate) {
            $endDate = $startDate->copy()->addMonths(3);
        }

        $this->update([
            'project_start_date' => $startDate,
            'project_end_date' => $endDate,
            'is_blocking_talent' => true,
            'blocking_notes' => "Talent blocked from {$startDate->format('M d, Y')} to {$endDate->format('M d, Y')} for project: {$this->project_title}"
        ]);
    }

    /**
     * Stop time-blocking for this talent
     */
    public function stopTimeBlocking(): void
    {
        $this->update([
            'is_blocking_talent' => false,
            'blocking_notes' => $this->blocking_notes . " - Project completed on " . now()->format('M d, Y')
        ]);
    }

    /**
     * Get formatted availability status
     */
    public function getAvailabilityStatus(): string
    {
        if (!$this->isCurrentlyBlockingTalent()) {
            return 'Available';
        }

        $endDate = $this->project_end_date->format('M d, Y');
        return "Busy until {$endDate}";
    }

    /**
     * Check if talent will be available by a certain date
     */
    public function isTalentAvailableBy($date): bool
    {
        if (!$this->is_blocking_talent) {
            return true;
        }

        return !$this->project_end_date || $date >= $this->project_end_date;
    }

    /**
     * Get next available date for this talent
     */
    public function getNextAvailableDate()
    {
        if (!$this->isCurrentlyBlockingTalent()) {
            return now();
        }

        return $this->project_end_date?->copy()->addDay();
    }

    /**
     * Parse project duration string to calculate end date
     */
    public static function parseDurationToMonths($duration): int
    {
        if (!$duration) return 1; // Default to 1 month

        $duration = strtolower($duration);

        if (str_contains($duration, 'week')) {
            preg_match('/(\d+)/', $duration, $matches);
            $weeks = isset($matches[0]) ? (int)$matches[0] : 1;
            return max(1, (int)($weeks / 4)); // Convert weeks to months (minimum 1 month)
        }

        if (str_contains($duration, 'month')) {
            preg_match('/(\d+)/', $duration, $matches);
            return isset($matches[0]) ? (int)$matches[0] : 1;
        }

        if (str_contains($duration, 'ongoing')) {
            return 12; // Default to 1 year for ongoing projects
        }

        // Default fallback
        return 1;
    }

    /**
     * Auto-calculate and set project dates based on duration
     */
    public function calculateProjectDates($startDate = null): void
    {
        $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $months = self::parseDurationToMonths($this->project_duration);

        $this->update([
            'project_start_date' => $startDate,
            'project_end_date' => $startDate->copy()->addMonths($months),
        ]);
    }

    /**
     * Check if a talent is available for a new project during specific dates
     */
    public static function isTalentAvailableForPeriod($talentId, $startDate, $endDate): bool
    {
        $conflictingRequests = self::where('talent_user_id', $talentId)
            ->where('is_blocking_talent', true)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('project_start_date', [$startDate, $endDate])
                      ->orWhereBetween('project_end_date', [$startDate, $endDate])
                      ->orWhere(function($subQuery) use ($startDate, $endDate) {
                          $subQuery->where('project_start_date', '<=', $startDate)
                                   ->where('project_end_date', '>=', $endDate);
                      });
            })
            ->exists();

        return !$conflictingRequests;
    }

    /**
     * Simple availability check - alias for isTalentAvailableForPeriod with default dates
     */
    public static function isTalentAvailable($talentId, $startDate = null, $endDate = null): bool
    {
        $startDate = $startDate ?: now()->addDays(7);
        $endDate = $endDate ?: $startDate->copy()->addMonths(1);

        return self::isTalentAvailableForPeriod($talentId, $startDate, $endDate);
    }

    /**
     * Get all active blocking requests for a talent
     */
    public static function getActiveBlockingRequestsForTalent($talentId)
    {
        return self::where('talent_user_id', $talentId)
            ->where('is_blocking_talent', true)
            ->where('project_end_date', '>', now())
            ->orderBy('project_end_date')
            ->get();
    }

    /**
     * Get cached talent availability status
     */
    public static function getCachedTalentAvailability($talentId): array
    {
        return cache()->remember("talent_availability_{$talentId}", 300, function() use ($talentId) {
            $activeRequests = self::getActiveBlockingRequestsForTalent($talentId);

            if ($activeRequests->isEmpty()) {
                return [
                    'available' => true,
                    'status' => 'Available',
                    'next_available_date' => null,
                    'blocking_projects' => []
                ];
            }

            $nextAvailable = $activeRequests->max('project_end_date');

            return [
                'available' => false,
                'status' => "Busy until " . $nextAvailable->format('M d, Y'),
                'next_available_date' => $nextAvailable->copy()->addDay(),
                'blocking_projects' => $activeRequests->map(function($req) {
                    return [
                        'title' => $req->project_title,
                        'end_date' => $req->project_end_date->format('M d, Y')
                    ];
                })->toArray()
            ];
        });
    }

    /**
     * Clear talent availability cache
     */
    public static function clearTalentAvailabilityCache($talentId): void
    {
        cache()->forget("talent_availability_{$talentId}");
    }

    /**
     * Boot method to clear cache when talent request is saved/deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($request) {
            if ($request->talent_user_id) {
                // Clear talent availability cache
                self::clearTalentAvailabilityCache($request->talent_user_id);

                // Clear discovery and recommendation caches
                Cache::forget("talent_recommendations_{$request->recruiter_id}_10");

                // Clear dashboard cache to show new requests immediately
                Cache::forget('talent_admin_dashboard_stats');
                Cache::forget('talent_admin_recent_activity');

                // Clear user-specific dashboard caches for all talent admins
                self::clearAllTalentAdminDashboardCaches();

                Cache::flush(); // Clear all discovery caches (they have complex keys)
            }
        });

        static::deleted(function ($request) {
            if ($request->talent_user_id) {
                // Clear talent availability cache
                self::clearTalentAvailabilityCache($request->talent_user_id);

                // Clear discovery and recommendation caches
                Cache::forget("talent_recommendations_{$request->recruiter_id}_10");

                // Clear dashboard caches
                Cache::forget('talent_admin_dashboard_stats');
                Cache::forget('talent_admin_recent_activity');

                // Clear user-specific dashboard caches for all talent admins
                self::clearAllTalentAdminDashboardCaches();

                Cache::flush(); // Clear all discovery caches
            }
        });
    }

    /**
     * Clear dashboard caches for all talent admin users
     */
    public static function clearAllTalentAdminDashboardCaches(): void
    {
        try {
            // Get all talent admin users
            $talentAdmins = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'talent_admin');
            })->get();

            // Clear user-specific cache for each admin
            foreach ($talentAdmins as $admin) {
                Cache::forget('talent_admin_recent_activity_' . $admin->id);
            }

            \Illuminate\Support\Facades\Log::info('Cleared dashboard caches for ' . $talentAdmins->count() . ' talent admins');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to clear talent admin dashboard caches: ' . $e->getMessage());
        }
    }

    /**
     * Get comprehensive workflow status that considers both formal status and acceptance states
     */
    public function getCurrentWorkflowStatus()
    {
        // If request is rejected, that's final
        if ($this->status === 'rejected') {
            return 'Request Rejected';
        }

        // If both parties have accepted, check formal status progression
        if ($this->both_parties_accepted) {
            switch ($this->status) {
                case 'meeting_arranged':
                    return 'Meeting Arranged - Both parties accepted';
                case 'agreement_reached':
                    return 'Agreement Reached';
                case 'onboarded':
                    return 'Talent Onboarded';
                case 'completed':
                    return 'Project Completed';
                default:
                    return 'Both parties accepted - Ready for meeting';
            }
        }

        // Check individual acceptance states
        if ($this->talent_accepted && $this->admin_accepted) {
            return 'Both parties accepted - Processing';
        } elseif ($this->talent_accepted && !$this->admin_accepted) {
            return 'Talent accepted - Waiting for admin approval';
        } elseif (!$this->talent_accepted && $this->admin_accepted) {
            return 'Admin approved - Waiting for talent acceptance';
        }

        // Default states based on formal status
        switch ($this->status) {
            case 'pending':
                return 'Pending Review';
            case 'approved':
                return 'Approved - Waiting for talent and admin acceptance';
            default:
                return $this->getFormattedStatus();
        }
    }

    /**
     * Get status for recruiter dashboard display - FIXED VERSION
     * This provides accurate, acceptance-aware status for recruiters
     */
    public function getRecruiterDisplayStatus()
    {
        // Show the most relevant status for recruiters
        if ($this->status === 'rejected') {
            return 'Request Rejected';
        }

        // Handle completed workflow states
        if ($this->both_parties_accepted) {
            switch ($this->status) {
                case 'meeting_arranged':
                    return 'Meeting Arranged';
                case 'agreement_reached':
                    return 'Agreement Reached';
                case 'onboarded':
                    return 'Talent Onboarded';
                case 'completed':
                    return 'Project Completed';
                default:
                    return 'Both Parties Accepted - Ready for Meeting';
            }
        }

        // Handle acceptance states - this is the key fix
        if ($this->talent_accepted && $this->admin_accepted) {
            return 'Both Parties Accepted - Processing';
        } elseif ($this->talent_accepted && !$this->admin_accepted) {
            return 'Talent Accepted - Awaiting Admin Review';
        } elseif (!$this->talent_accepted && $this->admin_accepted) {
            return 'Admin Pre-Approved - Awaiting Talent Response';
        } else {
            return 'Pending Review';
        }
    }

    /**
     * Get display status for frontend views
     * Normalizes internal status to user-friendly display status
     */
    public function getDisplayStatus()
    {
        // Handle specific cases first
        if ($this->status === 'rejected') {
            return 'rejected';
        }

        if ($this->both_parties_accepted) {
            return 'completed';
        }

        if ($this->talent_accepted && !$this->admin_accepted) {
            return 'accepted'; // Talent accepted, waiting for admin
        }

        if (!$this->talent_accepted && $this->admin_accepted) {
            return 'pending'; // Admin approved, waiting for talent
        }

        // Map internal statuses to display statuses
        $statusMap = [
            'pending' => 'pending',
            'approved' => 'accepted',
            'meeting_arranged' => 'accepted',
            'onboarded' => 'completed',
            'rejected' => 'rejected',
        ];

        return $statusMap[$this->status] ?? 'pending';
    }

    /**
     * Check if admin can proceed to arrange meeting
     * Both talent and admin must have explicitly accepted
     */
    public function canAdminArrangeMeeting()
    {
        // More flexible logic that handles different approval pathways
        $bothAccepted = $this->talent_accepted && $this->admin_accepted;
        $statusAllowsMeeting = in_array($this->status, ['approved']);
        $notRejected = $this->status !== 'rejected';

        return $bothAccepted && $statusAllowsMeeting && $notRejected;
    }

    /**
     * Get a unified, authoritative status display for consistent UX
     * This should be used across all frontend views
     */
    public function getUnifiedDisplayStatus()
    {
        // Handle rejected status first (terminal state)
        if ($this->status === 'rejected') {
            return 'Request Rejected';
        }

        // Handle completed workflow states
        switch ($this->status) {
            case 'meeting_arranged':
                return 'Meeting Arranged';
            case 'agreement_reached':
                return 'Agreement Reached';
            case 'onboarded':
                return 'Talent Onboarded';
            case 'completed':
                return 'Project Completed';
        }

        // Handle pending/approval states with acceptance logic
        if ($this->status === 'pending' || $this->status === 'approved') {
            if ($this->talent_accepted && $this->admin_accepted) {
                return 'Both Parties Accepted - Ready for Meeting';
            } elseif ($this->talent_accepted && !$this->admin_accepted) {
                return 'Talent Accepted - Awaiting Admin Approval';
            } elseif (!$this->talent_accepted && $this->admin_accepted) {
                return 'Admin Approved - Awaiting Talent Acceptance';
            } else {
                return 'Pending Review';
            }
        }

        // Fallback to formatted status
        return $this->getFormattedStatus();
    }

    /**
     * Get status badge color for recruiter displays - FIXED VERSION
     * This provides accurate, acceptance-aware badge colors for recruiters
     */
    public function getRecruiterStatusBadgeColor()
    {
        if ($this->status === 'rejected') {
            return 'danger';
        }

        // Handle completed workflow states
        if ($this->both_parties_accepted) {
            switch ($this->status) {
                case 'meeting_arranged':
                    return 'primary';
                case 'agreement_reached':
                case 'onboarded':
                case 'completed':
                    return 'success';
                default:
                    return 'info'; // Both accepted, ready for next step
            }
        }

        // Handle acceptance states with appropriate colors
        if ($this->talent_accepted && $this->admin_accepted) {
            return 'info'; // Both accepted, processing
        } elseif ($this->talent_accepted && !$this->admin_accepted) {
            return 'warning'; // Talent accepted, waiting for admin
        } elseif (!$this->talent_accepted && $this->admin_accepted) {
            return 'warning'; // Admin pre-approved, waiting for talent
        } else {
            return 'secondary'; // Pending review
        }
    }

    /**
     * Get status badge color for the unified display status
     */
    public function getUnifiedStatusBadgeColor()
    {
        if ($this->status === 'rejected') {
            return 'danger';
        }

        switch ($this->status) {
            case 'meeting_arranged':
                return 'primary';
            case 'agreement_reached':
            case 'onboarded':
            case 'completed':
                return 'success';
        }

        // For pending/approval states, use acceptance logic
        if ($this->status === 'pending' || $this->status === 'approved') {
            if ($this->talent_accepted && $this->admin_accepted) {
                return 'info'; // Ready for next step
            } elseif ($this->talent_accepted || $this->admin_accepted) {
                return 'warning'; // Partially accepted
            } else {
                return 'secondary'; // Pending
            }
        }

        return 'secondary';
    }

    /**
     * Get Tailwind CSS classes for status badges (for JavaScript usage)
     */
    public function getStatusBadgeColorClasses()
    {
        $colorMapping = [
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'info' => 'bg-blue-100 text-blue-800',
            'primary' => 'bg-indigo-100 text-indigo-800',
            'danger' => 'bg-red-100 text-red-800',
            'secondary' => 'bg-gray-100 text-gray-800'
        ];

        $colorType = $this->getUnifiedStatusBadgeColor();
        return $colorMapping[$colorType] ?? 'bg-gray-100 text-gray-800';
    }

    // ===================================================
    // PROJECT-CENTRIC SYSTEM MIGRATION METHODS
    // ===================================================

    /**
     * Check if this talent request has been migrated to the new project system
     */
    public function isMigratedToProjectSystem(): bool
    {
        return $this->migrated_to_project_system === true;
    }

    /**
     * Check if this talent request is eligible for migration
     */
    public function isEligibleForMigration(): bool
    {
        // Only migrate requests that are not completed/rejected
        return !in_array($this->status, ['completed', 'rejected']);
    }

    /**
     * Mark this talent request as migrated
     */
    public function markAsMigrated(int $projectId): bool
    {
        return $this->update([
            'project_id' => $projectId,
            'migrated_to_project_system' => true
        ]);
    }

    /**
     * Get scope for non-migrated talent requests
     */
    public function scopeNotMigrated($query)
    {
        return $query->where('migrated_to_project_system', false)
                    ->orWhereNull('migrated_to_project_system');
    }

    /**
     * Get scope for migrated talent requests
     */
    public function scopeMigrated($query)
    {
        return $query->where('migrated_to_project_system', true);
    }

    /**
     * Get scope for legacy (pre-project system) talent requests
     */
    public function scopeLegacy($query)
    {
        return $query->whereNull('project_id');
    }
}
