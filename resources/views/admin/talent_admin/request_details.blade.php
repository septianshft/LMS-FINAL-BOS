@extends('layout.template.mainTemplate')

@section('title', 'Request Details')
@section('container')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Request Details</h1>
        <a href="{{ route('talent_admin.manage_requests') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Requests
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Request Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Request Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Request ID:</label>
                                <p class="text-muted">#{{ $talentRequest->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status:</label>
                                <p>
                                    <span class="badge status-badge {{ $talentRequest->getBootstrapBadgeClasses() }}"
                                          style="font-size: 14px; padding: 8px 12px;">
                                        <i class="{{ $talentRequest->getStatusIcon() }} me-1"></i>
                                        {{ $talentRequest->getUnifiedDisplayStatus() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Project Title:</label>
                                <p class="text-muted">{{ $talentRequest->project_title ?? 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Collaboration Type:</label>
                                <p class="text-muted">{{ $talentRequest->collaboration_type ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($talentRequest->budget_range || $talentRequest->project_duration || $talentRequest->project_start_date || $talentRequest->project_end_date)
                    <div class="row">
                        @if($talentRequest->budget_range)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Budget Range:</label>
                                <p class="text-muted">{{ $talentRequest->budget_range }}</p>
                            </div>
                        </div>
                        @endif
                        @if($talentRequest->project_duration)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Project Duration:</label>
                                <p class="text-muted">{{ $talentRequest->project_duration }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($talentRequest->project_start_date || $talentRequest->project_end_date)
                    <div class="row">
                        @if($talentRequest->project_start_date)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Project Start Date:</label>
                                <p class="text-muted">
                                    {{ optional($talentRequest->project_start_date)->format('F d, Y') ?? 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        @endif
                        @if($talentRequest->project_end_date)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Project End Date:</label>
                                <p class="text-muted">
                                    {{ optional($talentRequest->project_end_date)->format('F d, Y') ?? 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Request Date:</label>
                                <p class="text-muted">
                                    {{ optional($talentRequest->created_at)->format('F d, Y \a\t H:i') ?? 'Not available' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Last Updated:</label>
                                <p class="text-muted">
                                    {{ optional($talentRequest->updated_at)->format('F d, Y \a\t H:i') ?? 'Not available' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @if($talentRequest->project_description)
                    <div class="form-group">
                        <label class="font-weight-bold">Project Description:</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $talentRequest->project_description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($talentRequest->requirements)
                    <div class="form-group">
                        <label class="font-weight-bold">Requirements:</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0">{{ $talentRequest->requirements }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Enhanced Project Timeline Information --}}
                    @if($talentRequest->project)
                    <hr class="my-4">
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-project-diagram me-2"></i>
                            Linked Project Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Project Name:</label>
                                    <p class="mb-1 text-dark">{{ $talentRequest->project->title }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Project Status:</label>
                                    <p class="mb-1">
                                        <span class="badge
                                            @if($talentRequest->project->status === 'active') badge-success
                                            @elseif($talentRequest->project->status === 'completed') badge-primary
                                            @elseif($talentRequest->project->status === 'overdue') badge-danger
                                            @elseif($talentRequest->project->status === 'pending_admin') badge-warning
                                            @else badge-secondary
                                            @endif">
                                            {{ ucwords(str_replace('_', ' ', $talentRequest->project->status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-alt me-1 text-success"></i>
                                        Project Start Date:
                                    </label>
                                    <p class="mb-1 text-dark">
                                        {{ optional($talentRequest->project->expected_start_date)->format('F d, Y') ?? 'Not set' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-calendar-times me-1 text-danger"></i>
                                        Project End Date:
                                    </label>
                                    <p class="mb-1 text-dark">
                                        <strong>{{ optional($talentRequest->project->expected_end_date)->format('F d, Y') ?? 'Not set' }}</strong>
                                        @if($talentRequest->project->expected_end_date)
                                            <br>
                                            <small class="text-muted">
                                                @php
                                                    $daysLeft = now()->diffInDays($talentRequest->project->expected_end_date, false);
                                                @endphp
                                                @if($daysLeft > 0)
                                                    <i class="fas fa-clock text-info"></i> {{ $daysLeft }} days remaining
                                                @elseif($daysLeft == 0)
                                                    <i class="fas fa-exclamation-triangle text-warning"></i> Due today
                                                @else
                                                    <i class="fas fa-exclamation-circle text-danger"></i> {{ abs($daysLeft) }} days overdue
                                                @endif
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if($talentRequest->project->estimated_duration_days)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-hourglass-half me-1 text-primary"></i>
                                        Estimated Duration:
                                    </label>
                                    <p class="mb-1 text-dark">{{ $talentRequest->project->estimated_duration_days }} days</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">
                                        <i class="fas fa-dollar-sign me-1 text-success"></i>
                                        Budget Range:
                                    </label>
                                    <p class="mb-1 text-dark">
                                        @if($talentRequest->project->overall_budget_min && $talentRequest->project->overall_budget_max)
                                            ${{ number_format($talentRequest->project->overall_budget_min) }} - ${{ number_format($talentRequest->project->overall_budget_max) }}
                                        @elseif($talentRequest->project->overall_budget_min)
                                            From ${{ number_format($talentRequest->project->overall_budget_min) }}
                                        @elseif($talentRequest->project->overall_budget_max)
                                            Up to ${{ number_format($talentRequest->project->overall_budget_max) }}
                                        @else
                                            Not specified
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($talentRequest->project->description)
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">
                                <i class="fas fa-info-circle me-1 text-info"></i>
                                Project Description:
                            </label>
                            <div class="bg-light p-3 rounded border">
                                <p class="mb-0 text-dark">{{ $talentRequest->project->description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Legacy Project Information from Talent Request --}}
                    @if(!$talentRequest->project && ($talentRequest->project_start_date || $talentRequest->project_end_date))
                    <hr class="my-4">
                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Request Timeline Information (Legacy)
                        </h6>
                        <div class="row">
                            @if($talentRequest->project_start_date)
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Requested Start Date:</label>
                                    <p class="mb-1 text-dark">
                                        {{ optional($talentRequest->project_start_date)->format('F d, Y') }}
                                    </p>
                                </div>
                            </div>
                            @endif
                            @if($talentRequest->project_end_date)
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold">Requested End Date:</label>
                                    <p class="mb-1 text-dark">
                                        <strong>{{ optional($talentRequest->project_end_date)->format('F d, Y') }}</strong>
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-exclamation-triangle"></i>
                            This request is not linked to the new project system. Timeline information is based on the original request data.
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if($talentRequest->status !== 'rejected' && $talentRequest->status !== 'completed')
                        <div class="d-grid gap-2 mb-3">
                            @if($talentRequest->status == 'pending')
                            <button type="button" class="btn btn-success btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'approved')">
                                <i class="fas fa-check me-1"></i>Approve Request
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'rejected')">
                                <i class="fas fa-times me-1"></i>Reject Request
                            </button>
                            @elseif($talentRequest->status == 'approved' && !$talentRequest->both_parties_accepted)
                                {{-- Admin approved but waiting for dual acceptance --}}
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <strong>Dual Acceptance Required:</strong><br>
                                            @if($talentRequest->talent_accepted && $talentRequest->admin_accepted)
                                                <span class="text-primary">✓ Both parties accepted - Processing...</span>
                                            @elseif($talentRequest->talent_accepted)
                                                <span class="text-warning">✓ Talent accepted - Waiting for admin acceptance</span>
                                            @elseif($talentRequest->admin_accepted)
                                                <span class="text-warning">✓ Admin approved - Waiting for talent acceptance</span>
                                            @else
                                                <span class="text-muted">Waiting for both talent and admin acceptance</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Show current acceptance status --}}
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            @if($talentRequest->talent_accepted)
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="text-success">Talent Accepted</span>
                                            @else
                                                <i class="fas fa-clock text-warning me-2"></i>
                                                <span class="text-warning">Talent Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            @if($talentRequest->admin_accepted)
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="text-success">Admin Accepted</span>
                                            @else
                                                <i class="fas fa-clock text-warning me-2"></i>
                                                <span class="text-warning">Admin Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Admin acceptance button if not yet accepted --}}
                                @if(!$talentRequest->admin_accepted)
                                    <button type="button" class="btn btn-primary btn-sm mb-2" onclick="acceptAsAdmin({{ $talentRequest->id }})">
                                        <i class="fas fa-thumbs-up me-1"></i>Accept as Admin
                                    </button>
                                @endif

                                {{-- Disabled meeting button with explanation --}}
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Both parties must accept before arranging meeting">
                                    <i class="fas fa-calendar me-1"></i>Arrange Meeting (Waiting for Acceptance)
                                </button>
                            @elseif($talentRequest->status == 'approved' && $talentRequest->canAdminArrangeMeeting())
                                {{-- Both parties accepted, can proceed to meeting --}}
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Both parties have accepted!</strong> Ready to arrange meeting.
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'meeting_arranged')">
                                    <i class="fas fa-calendar me-1"></i>Arrange Meeting
                                </button>
                            @elseif($talentRequest->status == 'meeting_arranged')
                            <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'agreement_reached')">
                                <i class="fas fa-handshake me-1"></i>Mark Agreement Reached
                            </button>
                            @elseif($talentRequest->status == 'agreement_reached')
                            <button type="button" class="btn btn-success btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'onboarded')">
                                <i class="fas fa-user-plus me-1"></i>Mark Onboarded
                            </button>
                            @elseif($talentRequest->status == 'onboarded')
                            <button type="button" class="btn btn-info btn-sm" onclick="updateStatus({{ $talentRequest->id }}, 'completed')">
                                <i class="fas fa-flag-checkered me-1"></i>Mark Completed
                            </button>
                            @endif
                        </div>
                    @endif

                    <hr>
                    <a href="{{ route('talent_admin.manage_requests') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-list me-1"></i> View All Requests
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recruiter Information -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Recruiter Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if(optional($talentRequest->recruiter->user)->avatar)
                            <img class="rounded-circle me-3" src="{{ asset('storage/' . $talentRequest->recruiter->user->avatar) }}"
                                 alt="{{ $talentRequest->recruiter->user->name ?? 'Recruiter' }}" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-info me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-building text-white fa-2x"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $talentRequest->recruiter->user->name ?? 'N/A' }}</h5>
                            <p class="text-muted mb-0">{{ $talentRequest->recruiter->user->pekerjaan ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Email:</strong></div>
                        <div class="col-sm-9">
                            @if($talentRequest->recruiter->user->email ?? null)
                                <a href="mailto:{{ $talentRequest->recruiter->user->email }}">
                                    {{ $talentRequest->recruiter->user->email }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Phone:</strong></div>
                        <div class="col-sm-9">{{ $talentRequest->recruiter->phone ?? $talentRequest->recruiter->user->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Company:</strong></div>
                        <div class="col-sm-9">{{ $talentRequest->recruiter->company_name ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Industry:</strong></div>
                        <div class="col-sm-9">{{ $talentRequest->recruiter->industry ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Status:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ optional($talentRequest->recruiter)->is_active ? 'success' : 'secondary' }}">
                                {{ optional($talentRequest->recruiter)->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Talent Information -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Talent Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if(optional($talentRequest->talent->user)->avatar)
                            <img class="rounded-circle me-3" src="{{ asset('storage/' . $talentRequest->talent->user->avatar) }}"
                                 alt="{{ $talentRequest->talent->user->name ?? 'Talent' }}" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-success me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-user text-white fa-2x"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $talentRequest->talent->user->name ?? 'N/A' }}</h5>
                            <p class="text-muted mb-0">{{ $talentRequest->talent->user->pekerjaan ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Email:</strong></div>
                        <div class="col-sm-9">
                            @if($talentRequest->talent->user->email ?? null)
                                <a href="mailto:{{ $talentRequest->talent->user->email }}">
                                    {{ $talentRequest->talent->user->email }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Phone:</strong></div>
                        <div class="col-sm-9">{{ $talentRequest->talent->user->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Skills:</strong></div>
                        <div class="col-sm-9">
                            @php
                                $talent = $talentRequest->talent ?? null;
                                $user = optional($talent)->user ?? null;
                                $skills = $user ? (method_exists($user, 'getTalentSkillsArray') ? $user->getTalentSkillsArray() : []) : [];
                                $skillCount = count($skills);
                            @endphp
                            @if($skillCount > 0)
                                <span class="badge bg-success">{{ $skillCount }} Verified Skills</span>
                                <div class="mt-2">
                                    @foreach(array_slice($skills, 0, 5) as $skill)
                                        @if(is_array($skill) && isset($skill['skill_name']))
                                            <span class="badge bg-light text-dark me-1">{{ $skill['skill_name'] }} ({{ ucfirst($skill['proficiency'] ?? 'intermediate') }})</span>
                                        @endif
                                    @endforeach
                                    @if($skillCount > 5)
                                        <span class="text-muted">+{{ $skillCount - 5 }} more</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">No skills verified yet</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Status:</strong></div>
                        <div class="col-sm-9">
                            <span class="badge bg-{{ optional($talentRequest->talent)->is_active ? 'success' : 'secondary' }}">
                                {{ optional($talentRequest->talent)->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($talentRequest->status === 'completed' && $talentRequest->workflow_completed_at)
    <!-- Completion Details -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-check-circle me-2"></i>
                        Completion Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Completed At:</label>
                                <p class="text-muted">
                                    {{ optional($talentRequest->workflow_completed_at)->format('d M Y, H:i') ?? 'Not available' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Duration:</label>
                                <p class="text-muted">
                                    @if($talentRequest->created_at && $talentRequest->workflow_completed_at)
                                        {{ $talentRequest->created_at->diffForHumans($talentRequest->workflow_completed_at, true) }}
                                    @else
                                        Not available
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($talentRequest->project_id)
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3"></i>
                                    <div>
                                        <strong>Automatic Completion</strong><br>
                                        This talent request was automatically marked as completed due to project closure approval.
                                        @if($talentRequest->project)
                                            <br><strong>Project:</strong> {{ $talentRequest->project->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-check me-3"></i>
                                    <div>
                                        <strong>Talent Availability</strong><br>
                                        This talent is now available for new recruitment requests.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function updateStatus(requestId, status) {
    // Prevent unauthorized meeting arrangement
    if (status === 'meeting_arranged') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('CSRF token not found. Please refresh the page.', 'danger');
            return;
        }

        // First check if both parties have accepted via AJAX
        fetch(`/talent-admin/request/${requestId}/can-arrange-meeting`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.canArrangeMeeting) {
                showAlert('Cannot arrange meeting: ' + data.reason, 'warning');
                return;
            }
            // Proceed with meeting arrangement
            performStatusUpdate(requestId, status);
        })
        .catch(error => {
            console.error('Error checking meeting eligibility:', error);
            showAlert('Error validating meeting arrangement eligibility.', 'danger');
        });
    } else {
        // For other status updates, proceed normally
        performStatusUpdate(requestId, status);
    }
}

function performStatusUpdate(requestId, status) {
    // Show loading state on all action buttons
    const actionButtons = document.querySelectorAll('[onclick^="updateStatus"], [onclick^="acceptAsAdmin"]');
    const originalButtonStates = [];

    actionButtons.forEach((btn, index) => {
        originalButtonStates[index] = {
            disabled: btn.disabled,
            innerHTML: btn.innerHTML
        };
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showAlert('CSRF token not found. Please refresh the page.', 'danger');
        return;
    }

    fetch(`/talent-admin/request/${requestId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update status badge
            const statusBadge = document.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `badge status-badge ${getStatusClass(status)}`;
                statusBadge.textContent = getStatusText(status);
            }

            // Show success message with additional info
            let message = 'Status updated successfully!';
            if (data.acceptance_status) {
                message += `\nAcceptance Status: ${data.acceptance_status}`;
            }
            showAlert(message, 'success');

            // Reload page after short delay to refresh button visibility
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating status: ' + error.message, 'danger');

        // Restore button states
        actionButtons.forEach((btn, index) => {
            if (originalButtonStates[index]) {
                btn.disabled = originalButtonStates[index].disabled;
                btn.innerHTML = originalButtonStates[index].innerHTML;
            }
        });
    });
}

function acceptAsAdmin(requestId) {
    if (!confirm('Are you sure you want to accept this request as an admin?')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showAlert('CSRF token not found. Please refresh the page.', 'danger');
        return;
    }

    fetch(`/talent-admin/request/${requestId}/admin-accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Admin acceptance recorded successfully!', 'success');

            // Show updated acceptance status
            if (data.both_parties_accepted) {
                showAlert('Both parties have now accepted! Meeting can be arranged.', 'info');
            }

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('Error: ' + (data.message || 'Failed to record admin acceptance'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error accepting as admin:', error);
        showAlert('Network error occurred. Please try again.', 'danger');
    });
}

function getStatusClass(status) {
    // Use backend status configuration mapping
    const statusClasses = {
        'pending': 'bg-warning text-dark',
        'approved': 'bg-info',
        'meeting_arranged': 'bg-primary',
        'agreement_reached': 'bg-success',
        'onboarded': 'bg-success',
        'rejected': 'bg-danger',
        'completed': 'bg-secondary'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getStatusText(status) {
    // Use backend status configuration mapping
    const statusTexts = {
        'pending': 'Pending Review',
        'approved': 'Approved by Admin',
        'meeting_arranged': 'Meeting Arranged',
        'agreement_reached': 'Agreement Reached',
        'onboarded': 'Talent Onboarded',
        'rejected': 'Request Rejected',
        'completed': 'Project Completed'
    };
    return statusTexts[status] || status.charAt(0).toUpperCase() + status.slice(1);
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message.replace(/\n/g, '<br>')}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const container = document.querySelector('.container-fluid');
    if (container) {
        container.prepend(alertDiv);

        // Auto-dismiss after 4 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 4000);
    }
}
</script>
@endsection
