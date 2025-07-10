<?php

namespace App\DTOs;

use App\Models\Project;
use Illuminate\Support\Collection;

class ProjectViewDataDTO
{
    public function __construct(
        public Project $project,
        public Collection $availableTalents,
        public Collection $talentInteractions,
        public bool $isRecruiter,
        public bool $isProjectOwner,
        public bool $canEdit,
        public bool $canDelete,
        public bool $canRequestExtension,
        public bool $canRequestClosure
    ) {}

    /**
     * Create DTO from project and user context
     */
    public static function fromProject(
        Project $project,
        Collection $availableTalents,
        Collection $talentInteractions,
        $user
    ): self {
        $recruiter = $user->recruiter;
        $isRecruiter = (bool) $recruiter;
        $isProjectOwner = $isRecruiter && $recruiter->id === $project->recruiter_id;
        
        return new self(
            project: $project,
            availableTalents: $availableTalents,
            talentInteractions: $talentInteractions,
            isRecruiter: $isRecruiter,
            isProjectOwner: $isProjectOwner,
            canEdit: $isProjectOwner && $project->status === Project::STATUS_PENDING_APPROVAL,
            canDelete: $isProjectOwner && $project->status === Project::STATUS_PENDING_APPROVAL,
            canRequestExtension: $isProjectOwner && in_array($project->status, [Project::STATUS_ACTIVE, Project::STATUS_OVERDUE]),
            canRequestClosure: $isProjectOwner && in_array($project->status, [Project::STATUS_ACTIVE, Project::STATUS_OVERDUE])
        );
    }

    /**
     * Check if project can have talent requests
     */
    public function canRequestTalents(): bool
    {
        return $this->isRecruiter && 
               $this->isProjectOwner && 
               $this->project->status === 'approved' &&
               $this->availableTalents->count() > 0;
    }

    /**
     * Get project status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->project->status) {
            'pending_admin' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'overdue' => 'bg-red-100 text-red-800',
            'closure_requested' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted project status
     */
    public function getFormattedStatus(): string
    {
        return match($this->project->status) {
            'pending_admin' => 'Menunggu Persetujuan Admin',
            'approved' => 'Disetujui',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'overdue' => 'Terlambat',
            'closure_requested' => 'Permintaan Penutupan',
            default => ucwords(str_replace('_', ' ', $this->project->status))
        };
    }

    /**ProjectViewDataDTO/**
     * Check if project can be edited
     */
    public function canEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * Check if project can request extension
     */
    public function canRequestExtension(): bool
    {
        return $this->canRequestExtension;
    }

    /**
     * Check if project can request closure
     */
    public function canRequestClosure(): bool
    {
        return $this->canRequestClosure;
    }

    /**
     * Check if project has any interactions
     */
    public function hasInteractions(): bool
    {
        return $this->talentInteractions->count() > 0;
    }

    /**
     * Get tab counts for navigation
     */
    public function getTabCounts(): array
    {
        return [
            'assignments' => $this->talentInteractions->count(),
            'timeline' => $this->project->timelineEvents->count(),
            'extensions' => $this->project->extensions->count()
        ];
    }
}