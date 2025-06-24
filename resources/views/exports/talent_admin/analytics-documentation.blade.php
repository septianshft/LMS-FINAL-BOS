<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Talent Admin Analytics Export' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { color: #1a237e; margin-bottom: 0.5em; }
        .section { margin-bottom: 2em; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: left; }
        th { background: #e3e6f3; }
        .status-badge { padding: 2px 8px; border-radius: 4px; font-size: 11px; color: #fff; }
        .status-ongoing { background: #1976d2; }
        .status-pending { background: #ffa000; }
        .status-completed { background: #388e3c; }
        .status-rejected { background: #d32f2f; }
        .small { font-size: 11px; color: #666; }
        .skills-list { list-style: none; padding: 0; margin: 0; }
        .skills-list li { display: inline-block; background: #f1f8e9; color: #33691e; border-radius: 3px; padding: 2px 7px; margin: 2px 2px 2px 0; font-size: 11px; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Talent Admin Analytics Export' }}</h1>
    <div class="section">
        <strong>Exported by:</strong> {{ $user->name }} ({{ $user->email }})<br>
        <strong>Role:</strong> {{ $roles ?? 'Talent Admin' }}<br>
        <strong>Export Date:</strong> {{ $exportDate ?? now()->format('d M Y H:i') }}
    </div>

    <div class="section">
        <h2>Dashboard Statistics</h2>
        <table>
            <tr>
                <th>Active Talents</th>
                <th>Available Talents</th>
                <th>Active Recruiters</th>
                <th>Total Requests</th>
                <th>Pending</th>
                <th>Approved</th>
                <th>Rejected</th>
            </tr>
            <tr>
                <td>{{ $dashboardStats['activeTalents'] ?? '-' }}</td>
                <td>{{ $dashboardStats['availableTalents'] ?? '-' }}</td>
                <td>{{ $dashboardStats['activeRecruiters'] ?? '-' }}</td>
                <td>{{ $dashboardStats['totalRequests'] ?? '-' }}</td>
                <td>{{ $dashboardStats['pendingRequests'] ?? '-' }}</td>
                <td>{{ $dashboardStats['approvedRequests'] ?? '-' }}</td>
                <td>{{ $dashboardStats['rejectedRequests'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Skill Analytics Summary</h2>
        @if(!empty($skillAnalytics))
            <ul>
                <li><strong>Top Skills:</strong> {{ implode(', ', $skillAnalytics['top_skills'] ?? []) }}</li>
                <li><strong>Skill Categories:</strong> {{ implode(', ', $skillAnalytics['skill_categories'] ?? []) }}</li>
                <li><strong>Market Demand:</strong> {{ $skillAnalytics['market_demand_analysis']['summary'] ?? '-' }}</li>
            </ul>
        @else
            <span class="small">No skill analytics data available.</span>
        @endif
    </div>

    <div class="section">
        <h2>Conversion Analytics Summary</h2>
        @if(!empty($conversionAnalytics))
            <ul>
                <li><strong>Top Conversion Candidates:</strong> {{ implode(', ', array_column($conversionAnalytics['top_conversion_candidates'] ?? [], 'name')) }}</li>
                <li><strong>Conversion Rate:</strong> {{ $conversionAnalytics['conversion_rate'] ?? '-' }}%</li>
            </ul>
        @else
            <span class="small">No conversion analytics data available.</span>
        @endif
    </div>

    <div class="section">
        <h2>All Talents</h2>
        <table>
            <thead>
                <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Redflag</th>
                <th>Skills</th>
                <th>Joined</th>
                </tr>
            </thead>
            <tbody>
            @foreach($talents as $talentUser)
                <tr>
                    <td>{{ $talentUser->name }}</td>
                    <td>{{ $talentUser->email }}</td>
                    <td>{{ $talentUser->talent && $talentUser->talent->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        @if($talentUser->talent && $talentUser->talent->redflagged)
                            <span style="color:#fff; background:#d32f2f; border-radius:4px; padding:2px 8px; font-size:11px;">Redflagged</span>
                            <div style="font-size:10px; color:#b71c1c; margin-top:2px;">{{ $talentUser->talent->redflag_reason }}</div>
                        @else
                            <span style="color:#888; font-size:11px;">-</span>
                        @endif
                    </td>
                    <td>
                        <ul class="skills-list">
                        @foreach($talentUser->getTalentSkillsArray() as $skill)
                            <li>{{ is_array($skill) ? ($skill['skill_name'] ?? ($skill['name'] ?? 'Unknown')) : $skill }}</li>
                        @endforeach
                        </ul>
                    </td>
                    <td>{{ $talentUser->created_at ? $talentUser->created_at->format('d M Y') : '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Talent Requests / Projects</h2>
        <table>
            <thead>
                <tr>
                    <th>Project Title</th>
                    <th>Recruiter</th>
                    <th>Talent</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Update</th>
                </tr>
            </thead>
            <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->project_title ?? '-' }}</td>
                    <td>{{ $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : '-' }}</td>
                    <td>{{ $request->talent && $request->talent->user ? $request->talent->user->name : '-' }}</td>
                    <td>
                        @php
                            $status = strtolower($request->status);
                            $badgeClass = 'status-badge ';
                            if($status === 'pending') $badgeClass .= 'status-pending';
                            elseif($status === 'approved' || $status === 'meeting_arranged' || $status === 'onboarded') $badgeClass .= 'status-ongoing';
                            elseif($status === 'completed') $badgeClass .= 'status-completed';
                            elseif($status === 'rejected') $badgeClass .= 'status-rejected';
                            else $badgeClass .= 'status-pending';
                        @endphp
                        <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                    </td>
                    <td>{{ $request->created_at ? $request->created_at->format('d M Y') : '-' }}</td>
                    <td>{{ $request->updated_at ? $request->updated_at->format('d M Y') : '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="section small">
        <em>Generated by WebPelatihan Talent Admin System &copy; {{ date('Y') }}</em>
    </div>
</body>
</html>
