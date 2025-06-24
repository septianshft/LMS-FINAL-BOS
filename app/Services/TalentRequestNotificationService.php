<?php

namespace App\Services;

use App\Models\TalentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TalentRequestNotificationService
{
    /**
     * Send notifications when a new talent request is submitted
     */
    public function notifyNewTalentRequest(TalentRequest $talentRequest): array
    {
        $notificationsSent = [];

        try {
            // Notify the talent (target of the request)
            $talentNotified = $this->notifyTalent($talentRequest);
            if ($talentNotified) {
                $notificationsSent[] = 'talent';
            }

            // Notify all talent admins
            $adminNotified = $this->notifyTalentAdmins($talentRequest);
            if ($adminNotified) {
                $notificationsSent[] = 'admin';
            }

            // Log the notification event
            Log::info('Talent request notifications sent', [
                'request_id' => $talentRequest->id,
                'notifications_sent' => $notificationsSent,
                'talent_id' => $talentRequest->talent_id,
                'recruiter_id' => $talentRequest->recruiter_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send talent request notifications', [
                'request_id' => $talentRequest->id,
                'error' => $e->getMessage()
            ]);
        }

        return $notificationsSent;
    }
      /**
     * Notify the talent about a new request
     */
    private function notifyTalent(TalentRequest $talentRequest): bool
    {
        try {
            $talent = $talentRequest->talent;
            if (!$talent || !$talent->user) {
                return false;
            }
              // Store notification that will be shown when the talent logs in next
            // For now using session flash, but this could be enhanced with database notifications
            if (Auth::check() && Auth::id() === $talent->user->id) {
                session()->flash('talent_request_notification', [
                    'type' => 'new_request',
                    'title' => 'New Project Request!',
                    'message' => "You have received a new collaboration request from {$talentRequest->recruiter->user->name} for '{$talentRequest->project_title}'.",
                    'request_id' => $talentRequest->id,
                    'recruiter_name' => $talentRequest->recruiter->user->name,
                    'project_title' => $talentRequest->project_title,
                    'created_at' => $talentRequest->created_at->format('M d, Y H:i'),
                    'user_id' => $talent->user->id
                ]);
            }

            // Log the notification for tracking
            Log::info('Talent notification queued', [
                'talent_id' => $talent->id,
                'talent_user_id' => $talent->user->id,
                'request_id' => $talentRequest->id,
                'project_title' => $talentRequest->project_title
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to notify talent', [
                'talent_id' => $talentRequest->talent_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
      /**
     * Notify all talent admins about a new request
     */
    private function notifyTalentAdmins(TalentRequest $talentRequest): bool
    {
        try {
            // Find all users with talent admin role
            $talentAdmins = User::whereHas('roles', function($query) {
                $query->where('name', 'talent_admin');
            })->get();

            if ($talentAdmins->isEmpty()) {
                return false;
            }
              // Store notification for currently logged-in admin (if any)
            if (Auth::check() && Auth::user()->roles()->where('name', 'talent_admin')->exists()) {
                session()->flash('admin_request_notification', [
                    'type' => 'new_request',
                    'title' => 'New Talent Request Pending!',
                    'message' => "A new talent request has been submitted by {$talentRequest->recruiter->user->name} for talent {$talentRequest->talent->user->name}.",
                    'request_id' => $talentRequest->id,
                    'recruiter_name' => $talentRequest->recruiter->user->name,
                    'talent_name' => $talentRequest->talent->user->name,
                    'project_title' => $talentRequest->project_title,
                    'created_at' => $talentRequest->created_at->format('M d, Y H:i'),
                    'admin_count' => $talentAdmins->count()
                ]);
            }

            // Log the notification for tracking
            Log::info('Admin notifications queued', [
                'request_id' => $talentRequest->id,
                'project_title' => $talentRequest->project_title,
                'admin_count' => $talentAdmins->count(),
                'notified_current_admin' => Auth::check() && Auth::user()->roles()->where('name', 'talent_admin')->exists()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to notify talent admins', [
                'request_id' => $talentRequest->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notify when status changes occur
     */
    public function notifyStatusChange(TalentRequest $talentRequest, string $oldStatus, string $newStatus): array
    {
        $notificationsSent = [];

        try {
            // Notify recruiter about status changes
            $recruiterNotified = $this->notifyRecruiterStatusChange($talentRequest, $oldStatus, $newStatus);
            if ($recruiterNotified) {
                $notificationsSent[] = 'recruiter';
            }

            // Notify talent about important status changes
            if (in_array($newStatus, ['approved', 'meeting_arranged', 'agreement_reached', 'onboarded'])) {
                $talentNotified = $this->notifyTalentStatusChange($talentRequest, $oldStatus, $newStatus);
                if ($talentNotified) {
                    $notificationsSent[] = 'talent';
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to send status change notifications', [
                'request_id' => $talentRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }

        return $notificationsSent;
    }

    /**
     * Notify recruiter about status changes
     */
    private function notifyRecruiterStatusChange(TalentRequest $talentRequest, string $oldStatus, string $newStatus): bool
    {
        try {
            $statusMessages = [
                'approved' => 'Your talent request has been approved by the admin! Next step is meeting arrangement.',
                'rejected' => 'Your talent request has been declined. You may try with different requirements.',
                'meeting_arranged' => 'A meeting has been arranged between you and the talent.',
                'agreement_reached' => 'Agreement has been reached! The talent will begin onboarding.',
                'onboarded' => 'The talent has been successfully onboarded to your project.',
                'completed' => 'Your project with the talent has been marked as completed.'
            ];

            if (!isset($statusMessages[$newStatus])) {
                return false;
            }

            session()->flash('recruiter_status_notification', [
                'type' => 'status_change',
                'title' => 'Request Status Updated',
                'message' => $statusMessages[$newStatus],
                'request_id' => $talentRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'talent_name' => $talentRequest->talent->user->name,
                'project_title' => $talentRequest->project_title,
                'user_id' => $talentRequest->recruiter->user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to notify recruiter of status change', [
                'request_id' => $talentRequest->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notify talent about status changes
     */
    private function notifyTalentStatusChange(TalentRequest $talentRequest, string $oldStatus, string $newStatus): bool
    {
        try {
            $statusMessages = [
                'approved' => 'Great news! Your collaboration request has been approved. A meeting will be arranged soon.',
                'meeting_arranged' => 'A meeting has been scheduled with the recruiter. Check your contact details.',
                'agreement_reached' => 'Agreement reached! You can now begin the onboarding process.',
                'onboarded' => 'Welcome! You have been successfully onboarded to the project.'
            ];

            if (!isset($statusMessages[$newStatus])) {
                return false;
            }

            session()->flash('talent_status_notification', [
                'type' => 'status_change',
                'title' => 'Request Status Updated',
                'message' => $statusMessages[$newStatus],
                'request_id' => $talentRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'recruiter_name' => $talentRequest->recruiter->user->name,
                'project_title' => $talentRequest->project_title,
                'user_id' => $talentRequest->talent->user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to notify talent of status change', [
                'request_id' => $talentRequest->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
