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
            border-bottom: 2px solid #6f42c1;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #6f42c1;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
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
            font-size: 20px;
            font-weight: bold;
            color: #6f42c1;
        }
        .stat-label {
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .talent-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .talent-table th {
            background-color: #6f42c1;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        .talent-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }
        .talent-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .talent-table tr:hover {
            background-color: #e9ecef;
        }
        .talent-name {
            font-weight: bold;
            color: #333;
        }
        .talent-email {
            color: #666;
            font-size: 10px;
        }
        .metric-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: bold;
            margin: 1px;
        }
        .badge-total {
            background-color: #e9ecef;
            color: #495057;
        }
        .badge-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .project-list {
            max-width: 200px;
            word-wrap: break-word;
            font-size: 10px;
            color: #666;
        }
        .high-performer {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .top-talent {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        .performance-indicators {
            margin-bottom: 20px;
            font-size: 11px;
        }
        .indicator {
            display: inline-block;
            margin-right: 20px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .indicator-legend {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        $totalTalents = $talentParticipation->count();
        $totalProjects = $talentParticipation->sum('total_projects');
        $totalAccepted = $talentParticipation->sum('accepted_projects');
        $totalCompleted = $talentParticipation->sum('completed_projects');
        $avgParticipation = $totalTalents > 0 ? $totalProjects / $totalTalents : 0;
    @endphp

    <div class="summary-stats">
        <div class="stat-box">
            <div class="stat-number">{{ $totalTalents }}</div>
            <div class="stat-label">Unique Talents</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $totalProjects }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $totalAccepted }}</div>
            <div class="stat-label">Accepted Projects</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ number_format($avgParticipation, 1) }}</div>
            <div class="stat-label">Avg Projects/Talent</div>
        </div>
    </div>

    <div class="indicator-legend">
        <strong>Performance Indicators:</strong>
        <span class="indicator top-talent">🌟 Top Talent</span> = 3+ completed projects
        <span class="indicator high-performer">⭐ High Performer</span> = 2+ completed projects
    </div>

    @if($talentParticipation->count() > 0)
        <table class="talent-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Talent Details</th>
                    <th style="width: 15%;">Project Metrics</th>
                    <th style="width: 15%;">Performance</th>
                    <th style="width: 45%;">Participated Projects</th>
                </tr>
            </thead>
            <tbody>
                @foreach($talentParticipation->sortBy('total_projects')->reverse() as $participation)
                    @php
                        $isTopTalent = $participation->completed_projects >= 3;
                        $isHighPerformer = $participation->completed_projects >= 2 && !$isTopTalent;
                        $acceptanceRate = $participation->total_projects > 0
                            ? ($participation->accepted_projects / $participation->total_projects) * 100
                            : 0;
                        $completionRate = $participation->accepted_projects > 0
                            ? ($participation->completed_projects / $participation->accepted_projects) * 100
                            : 0;
                    @endphp
                    <tr class="{{ $isTopTalent ? 'top-talent' : ($isHighPerformer ? 'high-performer' : '') }}">
                        <td>
                            <div class="talent-name">
                                @if($isTopTalent) 🌟 @elseif($isHighPerformer) ⭐ @endif
                                {{ $participation->talent_name }}
                            </div>
                            <div class="talent-email">{{ $participation->talent_email }}</div>
                            <div style="margin-top: 5px; font-size: 10px; color: #666;">
                                ID: {{ $participation->talent_id }}
                            </div>
                        </td>
                        <td>
                            <div class="metric-badge badge-total">
                                {{ $participation->total_projects }} Total
                            </div>
                            <div class="metric-badge badge-accepted">
                                {{ $participation->accepted_projects }} Accepted
                            </div>
                            <div class="metric-badge badge-completed">
                                {{ $participation->completed_projects }} Completed
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 10px; margin-bottom: 3px;">
                                <strong>Acceptance:</strong> {{ number_format($acceptanceRate, 1) }}%
                            </div>
                            <div style="font-size: 10px;">
                                <strong>Completion:</strong> {{ number_format($completionRate, 1) }}%
                            </div>
                            @if($acceptanceRate >= 80)
                                <div style="color: #28a745; font-size: 9px; margin-top: 3px;">
                                    ✓ High Acceptance Rate
                                </div>
                            @endif
                            @if($completionRate >= 90)
                                <div style="color: #007bff; font-size: 9px; margin-top: 3px;">
                                    ✓ Excellent Completion
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="project-list">
                                {{ $participation->project_titles }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 30px; font-size: 11px; color: #666;">
            <h3 style="color: #6f42c1; margin-bottom: 10px;">Talent Performance Summary:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Top Talents (3+ completed):</strong> {{ $talentParticipation->where('completed_projects', '>=', 3)->count() }} talents</li>
                <li><strong>High Performers (2+ completed):</strong> {{ $talentParticipation->where('completed_projects', '>=', 2)->count() }} talents</li>
                <li><strong>Most Active Talent:</strong> {{ $talentParticipation->sortBy('total_projects')->last()->talent_name ?? 'N/A' }} ({{ $talentParticipation->max('total_projects') }} projects)</li>
                <li><strong>Best Completion Rate:</strong> {{ number_format($talentParticipation->max(function($t) { return $t->accepted_projects > 0 ? ($t->completed_projects / $t->accepted_projects) * 100 : 0; }), 1) }}%</li>
            </ul>
        </div>
    @else
        <div class="no-data">
            No talent participation data found for this recruiter.
        </div>
    @endif

    <div class="footer">
        <p>Generated by {{ config('app.name') }} • {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>This report analyzes talent participation across {{ $totalProjects }} project assignments involving {{ $totalTalents }} unique talents.</p>
    </div>
</body>
</html>
