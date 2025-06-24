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
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .project {
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            break-inside: avoid;
        }
        .project-header {
            background-color: #f8f9fa;
            margin: -15px -15px 15px -15px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .project-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin: 0 0 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d1ecf1; color: #0c5460; }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-completed { background-color: #f8f9fa; color: #6c757d; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
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
        .talents-section {
            margin-top: 15px;
        }
        .talents-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .talent-item {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 3px;
            font-size: 11px;
        }
        .talent-name {
            font-weight: bold;
            color: #333;
        }
        .talent-status {
            float: right;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
        }
        .talent-accepted { background-color: #d4edda; color: #155724; }
        .talent-pending { background-color: #fff3cd; color: #856404; }
        .talent-declined { background-color: #f8d7da; color: #721c24; }
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
        <p><strong>Total Projects:</strong> {{ $projects->count() }}</p>
    </div>

    @if($projects->count() > 0)
        @foreach($projects as $project)
            <div class="project">
                <div class="project-header">
                    <div class="project-title">{{ $project->title }}</div>
                    <span class="status-badge status-{{ str_replace('_', '-', $project->status) }}">
                        {{ ucwords(str_replace('_', ' ', $project->status)) }}
                    </span>
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
                        <div class="detail-item">
                            <span class="detail-label">Duration:</span>
                            {{ $project->estimated_duration_days }} days
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
                        <div class="detail-item">
                            <span class="detail-label">Assignments:</span>
                            {{ $project->assignments->count() }} total
                        </div>
                    </div>
                </div>

                @if($project->description)
                    <div class="detail-item" style="margin-bottom: 15px;">
                        <span class="detail-label">Description:</span>
                        {{ Str::limit($project->description, 200) }}
                    </div>
                @endif

                @if($project->assignments->count() > 0)
                    <div class="talents-section">
                        <div class="talents-title">Participating Talents ({{ $project->assignments->count() }})</div>
                        @foreach($project->assignments as $assignment)
                            <div class="talent-item">
                                <span class="talent-name">{{ $assignment->talent->user->name }}</span>
                                <span class="talent-status talent-{{ $assignment->status }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                                <div style="clear: both; margin-top: 3px; color: #666;">
                                    {{ $assignment->talent->user->email }}
                                    @if($assignment->assigned_at)
                                        • Assigned: {{ $assignment->assigned_at->format('M d, Y') }}
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
            No projects found for this recruiter.
        </div>
    @endif

    <div class="footer">
        <p>Generated by {{ config('app.name') }} • {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>This report contains {{ $projects->count() }} project(s) with detailed talent participation information.</p>
    </div>
</body>
</html>
