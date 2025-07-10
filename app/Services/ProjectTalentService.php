<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProjectTalentService
{
    /**
     * Get all talent interactions for a project (assignments and requests)
     */
    public function getTalentInteractions(Project $project): Collection
    {
        $allInteractions = collect();
        $assignedTalentIds = collect();

        // Add assignments first and track assigned talent IDs
        foreach ($project->assignments as $assignment) {
            $assignedTalentIds->push($assignment->talent_id);
            $allInteractions->push($this->processAssignment($assignment));
        }

        // Add talent requests (only if not already assigned)
        foreach ($project->talentRequests as $request) {
            if (!$assignedTalentIds->contains($request->talent_id)) {
                $allInteractions->push($this->processTalentRequest($request, $project));
            }
        }

        return $allInteractions;
    }

    /**
     * Process assignment data
     */
    private function processAssignment($assignment): array
    {
        $startDate = $assignment->talent_start_date;
        $endDate = $assignment->talent_end_date;
        $durationData = $this->calculateDuration($startDate, $endDate);
        
        if ($assignment->isActive()) {
            $durationData['days_remaining'] = $assignment->getRemainingDays();
            $durationData['is_overdue'] = $assignment->isOverdue();
        }

        return [
            'type' => 'assignment',
            'id' => $assignment->id,
            'talent_id' => $assignment->talent_id,
            'name' => $assignment->talent->user->name ?? 'Unknown Talent',
            'role' => $assignment->specific_role,
            'status' => $assignment->status,
            'created_at' => $assignment->created_at,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'talent_data' => $this->getTalentData($assignment->talent),
            ...$durationData
        ];
    }

    /**
     * Process talent request data
     */
    private function processTalentRequest($request, Project $project): array
    {
        $name = $this->getTalentName($request);
        $durationData = $this->calculateRequestDuration($request, $project);

        return [
            'type' => 'talent_request',
            'id' => $request->id,
            'talent_id' => $request->talent_id,
            'name' => $name,
            'role' => null,
            'status' => $request->status,
            'created_at' => $request->created_at,
            'start_date' => $durationData['start_date'],
            'end_date' => $durationData['end_date'],
            'talent_data' => $this->getTalentData($request->talent),
            'duration_text' => $durationData['duration_text'],
            'days_remaining' => null,
            'is_overdue' => false,
            'total_days' => $durationData['total_days']
        ];
    }

    /**
     * Calculate duration information
     */
    private function calculateDuration($startDate, $endDate): array
    {
        $durationText = null;
        $totalDays = 0;
        $daysRemaining = null;
        $isOverdue = false;

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $totalDays = $start->diffInDays($end);
            $durationText = $totalDays . ' days';
        }

        return [
            'duration_text' => $durationText,
            'days_remaining' => $daysRemaining,
            'is_overdue' => $isOverdue,
            'total_days' => $totalDays
        ];
    }

    /**
     * Calculate duration for talent request
     */
    private function calculateRequestDuration($request, Project $project): array
    {
        $startDate = $request->project_start_date;
        $endDate = $request->project_end_date;
        $durationText = null;
        $totalDays = 0;

        // Extract duration from project_duration field if available
        if ($request->project_duration && preg_match('/(\d+)\s*(days?|hari)/i', $request->project_duration, $matches)) {
            $totalDays = (int)$matches[1];
            $durationText = $totalDays . ' hari';

            // Calculate correct end date based on duration
            if ($startDate && $totalDays > 0) {
                $calculatedEndDate = Carbon::parse($startDate)->addDays($totalDays);
                $endDate = $calculatedEndDate->format('Y-m-d H:i:s');
            }
        }
        // Fall back to calculating from stored dates
        elseif ($startDate && $endDate) {
            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            $durationText = $totalDays . ' days';
        }
        // Use project dates as fallback
        elseif ($project->expected_start_date && $project->expected_end_date) {
            $startDate = $project->expected_start_date;
            $endDate = $project->expected_end_date;
            $totalDays = $project->expected_start_date->diffInDays($project->expected_end_date);
            $durationText = $totalDays . ' days (from project timeline)';
        }
        // Display duration text as-is
        elseif ($request->project_duration) {
            $durationText = $request->project_duration;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_text' => $durationText,
            'total_days' => $totalDays
        ];
    }

    /**
     * Get talent name from request
     */
    private function getTalentName($request): string
    {
        if ($request->talentUser && $request->talentUser->name) {
            return $request->talentUser->name;
        } elseif ($request->talent && $request->talent->user && $request->talent->user->name) {
            return $request->talent->user->name;
        }
        return 'Unknown Talent';
    }

    /**
     * Get structured talent data for modal display
     */
    private function getTalentData($talent): ?array
    {
        if (!$talent || !$talent->user) {
            return null;
        }

        $user = $talent->user;
        
        return [
            'id' => $talent->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'location' => $user->alamat,
            'job' => $user->pekerjaan,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'is_active' => $talent->is_active,
            'skills' => $user->getTalentSkillsArray() ?? [],
            'joined_date' => $talent->created_at->locale('id')->translatedFormat('d F Y')
        ];
    }

    /**
     * Calculate progress percentage for active assignments
     */
    public function calculateProgress(array $interaction): int
    {
        if ($interaction['type'] !== 'assignment' || $interaction['status'] !== 'active') {
            return 0;
        }

        $totalDays = $interaction['total_days'] ?? 1;
        $daysRemaining = $interaction['days_remaining'] ?? 0;
        $daysElapsed = $totalDays - $daysRemaining;
        
        return $totalDays > 0 ? min(100, max(0, ($daysElapsed / $totalDays) * 100)) : 0;
    }

    /**
     * Get status color class for interaction
     */
    public function getStatusColorClass(string $status): string
    {
        return match($status) {
            'active' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get progress bar color class
     */
    public function getProgressColorClass(array $interaction): string
    {
        if ($interaction['is_overdue']) {
            return 'bg-red-500';
        }
        
        $daysRemaining = $interaction['days_remaining'] ?? 0;
        return $daysRemaining <= 7 ? 'bg-orange-500' : 'bg-blue-600';
    }

    /**
     * Get remaining days text color class
     */
    public function getRemainingDaysColorClass(array $interaction): string
    {
        if ($interaction['is_overdue']) {
            return 'text-red-600';
        }
        
        $daysRemaining = $interaction['days_remaining'] ?? 0;
        return $daysRemaining <= 7 ? 'text-orange-600' : 'text-green-600';
    }
}