<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .stat-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
        }
        .project {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            break-inside: avoid;
        }
        .project-header {
            background-color: #d4edda;
            margin: -15px -15px 15px -15px;
            padding: 15px;
            border-bottom: 1px solid #c3e6cb;
        }
        .project-title {
            font-size: 16px;
            font-weight: bold;
            color: #155724;
            margin: 0 0 5px 0;
        }
        .completion-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
        }
        .project-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .detail-item {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 100px;
        }
        .success-metrics {
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        .success-title {
            font-weight: bold;
            color: #155724;
            margin-bottom: 5px;
        }
        .talents-section {
            margin-top: 15px;
        }
        .talents-title {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
            border-bottom: 1px solid #c3e6cb;
            padding-bottom: 5px;
        }
        .talent-item {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 3px;
            font-size: 11px;
            border-left: 3px solid #28a745;
        }
        .talent-name {
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-projects {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><strong>Recruiter:</strong> {{ $recruiter->user->name }} ({{ $recruiter->user->email }})</p>
        <p><strong>Export Date:</strong> {{ $exportDate->format('F d, Y \a\t H:i') }}</p>
    </div>

    @php
        $totalTalents = $projects->flatMap->assignments->where('status', 'accepted')->count();
        $avgDuration = $projects->avg('estimated_duration_days');
        $totalBudget = $projects->sum(function($project) {
            return ($project->overall_budget_min + $project->overall_budget_max) / 2;
        });
    @endphp

    <div class="summary-stats">
        <div class="stat-box">
            <div class="stat-number">{{ $projects->count() }}</div>
            <div class="stat-label">Completed Projects</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $totalTalents }}</div>
            <div class="stat-label">Total Talents Engaged</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ number_format($avgDuration, 1) }}</div>
            <div class="stat-label">Avg Duration (Days)</div>
        </div>
    </div>

    @if($projects->count() > 0)
        @foreach($projects as $project)
            <div class="project">
                <div class="project-header">
                    <div class="project-title">{{ $project->title }}</div>
                    <span class="completion-badge">✓ COMPLETED</span>
                </div>

                <div class="success-metrics">
                    <div class="success-title">Project Success Metrics</div>
                    <div style="font-size: 11px;">
                        <strong>Completion Date:</strong> {{ $project->updated_at->format('M d, Y') }} •
                        <strong>Talents Accepted:</strong> {{ $project->assignments->where('status', 'accepted')->count() }}/{{ $project->assignments->count() }} •
                        <strong>Duration:</strong> {{ $project->estimated_duration_days }} days
                    </div>
                </div>

                <div class="project-details">
                    <div>
                        <div class="detail-item">
                            <span class="detail-label">Industry:</span>
                            {{ $project->industry ?? 'Not specified' }}
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Start Date:</span>
                            {{ $project->expected_start_date->format('M d, Y') }}
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">End Date:</span>
                            {{ $project->expected_end_date->format('M d, Y') }}
                        </div>
                    </div>
                    <div>
                        @if($project->overall_budget_min || $project->overall_budget_max)
                            <div class="detail-item">
                                <span class="detail-label">Budget:</span>
                                @if($project->overall_budget_min && $project->overall_budget_max)
                                    Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }} -
                                    Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                @elseif($project->overall_budget_min)
                                    From Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }}
                                @else
                                    Up to Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                @endif
                            </div>
                        @endif
                        <div class="detail-item">
                            <span class="detail-label">Created:</span>
                            {{ $project->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>

                @if($project->description)
                    <div class="detail-item" style="margin-bottom: 15px;">
                        <span class="detail-label">Description:</span>
                        {{ Str::limit($project->description, 200) }}
                    </div>
                @endif

                @if($project->assignments->where('status', 'accepted')->count() > 0)
                    <div class="talents-section">
                        <div class="talents-title">Successfully Engaged Talents ({{ $project->assignments->where('status', 'accepted')->count() }})</div>
                        @foreach($project->assignments->where('status', 'accepted') as $assignment)
                            <div class="talent-item">
                                <span class="talent-name">{{ $assignment->talent->user->name }}</span>
                                <div style="margin-top: 3px; color: #666;">
                                    {{ $assignment->talent->user->email }}
                                    @if($assignment->assigned_at)
                                        • Joined: {{ $assignment->assigned_at->format('M d, Y') }}
                                    @endif
                                    @if($assignment->accepted_at)
                                        • Accepted: {{ $assignment->accepted_at->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="no-projects">
            No completed projects found for this recruiter.
        </div>
    @endif

    <div class="footer">
        <p>Generated by {{ config('app.name') }} • {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>This report shows {{ $projects->count() }} successfully completed project(s) with talent engagement details.</p>
    </div>
</body>
</html>
