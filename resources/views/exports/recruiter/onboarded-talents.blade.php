<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarded Talents - {{ $recruiter_user->name }}</title>
    <style>
        /* Simple, ATS-friendly styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .summary {
            text-align: center;
            margin-bottom: 25px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .summary-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .talent-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #333;
        }

        .talent-header {
            background: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #333;
        }

        .talent-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .talent-title {
            font-size: 12px;
            margin: 2px 0 0 0;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f9f9f9;
            font-weight: bold;
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0 8px 0;
            font-size: 13px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            border: 1px solid #ddd;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Page break controls */
        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Onboarded Talents Report</h1>
        <p>Recruiter: {{ $recruiter_user->name }}</p>
        <p>Generated: {{ $export_date }}</p>
    </div>

    <div class="summary">
        <div class="summary-number">{{ $total_onboarded }}</div>
        <div>Successfully Onboarded Talents</div>
    </div>

    @if($onboarded_requests->count() > 0)
        @foreach($onboarded_requests as $index => $request)
            @if($index > 0)
                <div class="page-break"></div>
            @endif

            <div class="talent-section">
                <div class="talent-header">
                    <div class="talent-name">{{ $request->talent->user->name ?? 'N/A' }}</div>
                    <div class="talent-title">{{ $request->talent->user->pekerjaan ?? $request->project_title }} - Status: {{ ucfirst($request->status) }}</div>
                </div>

                <!-- Personal Information -->
                <div class="section-title">Personal Information</div>
                <table>
                    <tr>
                        <th width="25%">Full Name</th>
                        <td>{{ $request->talent->user->name ?? 'N/A' }}</td>
                        <th width="25%">Email</th>
                        <td>{{ $request->talent->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $request->talent->user->phone ?? 'Not provided' }}</td>
                        <th>Location</th>
                        <td>{{ $request->talent->user->location ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Experience Level</th>
                        <td colspan="3">{{ ucfirst($request->talent->user->experience_level ?? 'Not specified') }}</td>
                    </tr>
                    @if($request->talent->user->talent_bio)
                    <tr>
                        <th>Bio</th>
                        <td colspan="3">{{ $request->talent->user->talent_bio }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Skills -->
                @php
                    $skills = $request->talent->user->getTalentSkillsArray();
                @endphp
                @if(is_array($skills) && count($skills) > 0)
                <div class="section-title">Skills & Expertise</div>
                <table>
                    <tr>
                        <th>Skills</th>
                        <td>
                            @foreach($skills as $skill)
                                @if(is_array($skill))
                                    {{ $skill['skill_name'] ?? $skill['name'] ?? 'Unknown' }}@if(!$loop->last), @endif
                                @else
                                    {{ $skill }}@if(!$loop->last), @endif
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
                @endif

                <!-- Project Information -->
                <div class="section-title">Project Information</div>
                <table>
                    <tr>
                        <th width="25%">Project Title</th>
                        <td colspan="3">{{ $request->project_title }}</td>
                    </tr>
                    @if($request->project_description)
                    <tr>
                        <th>Description</th>
                        <td colspan="3">{{ $request->project_description }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Rentang Anggaran</th>
                        <td>{{ $request->budget_range ?? 'Tidak ditentukan' }}</td>
                        <th>Durasi</th>
                        <td>{{ $request->project_duration ? preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) : 'Tidak ditentukan' }}</td>
                    </tr>
                    @if($request->requirements)
                    <tr>
                        <th>Requirements</th>
                        <td colspan="3">{{ $request->requirements }}</td>
                    </tr>
                    @endif
                    @if($request->project_start_date && $request->project_end_date)
                    <tr>
                        <th>Project Timeline</th>
                        <td colspan="3">{{ \Carbon\Carbon::parse($request->project_start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->project_end_date)->format('d M Y') }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Timeline -->
                <div class="section-title">Collaboration Timeline</div>
                <table>
                    <tr>
                        <th width="25%">Request Submitted</th>
                        <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                        <th width="25%">Status</th>
                        <td>{{ ucfirst($request->status) }}</td>
                    </tr>
                    @if($request->approved_at)
                    <tr>
                        <th>Admin Approved</th>
                        <td>{{ \Carbon\Carbon::parse($request->approved_at)->format('d M Y, H:i') }}</td>
                        <th>Last Updated</th>
                        <td>{{ $request->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @endif
                </table>

                <!-- Performance Metrics -->
                @if($request->talent->user->completed_courses_count || $request->talent->user->certificates_count || $request->talent->user->average_quiz_score)
                <div class="section-title">Performance Metrics</div>
                <table>
                    <tr>
                        @if($request->talent->user->completed_courses_count)
                        <th>Completed Courses</th>
                        <td>{{ $request->talent->user->completed_courses_count }} courses</td>
                        @endif
                        @if($request->talent->user->certificates_count)
                        <th>Certificates Earned</th>
                        <td>{{ $request->talent->user->certificates_count }} certificates</td>
                        @endif
                    </tr>
                    @if($request->talent->user->average_quiz_score)
                    <tr>
                        <th>Average Quiz Score</th>
                        <td>{{ number_format($request->talent->user->average_quiz_score, 1) }}%</td>
                        <th>Member Since</th>
                        <td>{{ $request->talent->user->created_at->format('M Y') }}</td>
                    </tr>
                    @endif
                </table>
                @endif

                <!-- Portfolio -->
                @if($request->talent->user->portfolio_url)
                <div class="section-title">Portfolio & Contact</div>
                <table>
                    <tr>
                        <th width="25%">Portfolio URL</th>
                        <td colspan="3">{{ $request->talent->user->portfolio_url }}</td>
                    </tr>
                </table>
                @endif
            </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>No Onboarded Talents Found</h3>
            <p>You haven't successfully onboarded any talents yet.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report contains confidential information about successfully onboarded talents.</p>
        <p>&copy; {{ date('Y') }} WebPelatihan. All rights reserved.</p>
    </div>
</body>
</html>
