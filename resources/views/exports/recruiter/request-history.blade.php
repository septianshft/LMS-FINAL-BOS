<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent Request History - {{ $recruiter_user->name }}</title>
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

        .request-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #333;
        }

        .request-header {
            background: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #333;
            text-align: center;
        }

        .request-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .request-meta {
            font-size: 11px;
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
        <h1>Talent Request History</h1>
        <p>Report for {{ $recruiter_user->name }}</p>
        <p>Generated on {{ $export_date }}</p>
    </div>

    <div class="summary">
        <div class="summary-number">{{ $total_requests }}</div>
        <div>Total Requests</div>
    </div>

    @if($requests->count() > 0)
        @foreach($requests as $index => $request)
        @if($index > 0)
        <div class="page-break"></div>
        @endif

        <div class="request-section">
            <div class="request-header">
                <div class="request-title">{{ $request->project_title }}</div>
                <div class="request-meta">
                    Request ID: #{{ $request->id }} |
                    Date: {{ $request->created_at->format('d M Y') }} |
                    Status: {{ ucfirst($request->status) }}
                </div>
            </div>

            <div class="section-title">Talent Information</div>
            <table>
                <tr>
                    <th width="20%">Name</th>
                    <td>{{ $request->talent->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $request->talent->user->email ?? 'N/A' }}</td>
                </tr>
                @if($request->talent->user->phone)
                <tr>
                    <th>Phone</th>
                    <td>{{ $request->talent->user->phone }}</td>
                </tr>
                @endif
                @if($request->talent->user->pekerjaan)
                <tr>
                    <th>Profession</th>
                    <td>{{ $request->talent->user->pekerjaan }}</td>
                </tr>
                @endif
                @if($request->talent->user->experience_level)
                <tr>
                    <th>Experience Level</th>
                    <td>{{ ucfirst($request->talent->user->experience_level) }}</td>
                </tr>
                @endif
                @if($request->talent->user->location)
                <tr>
                    <th>Location</th>
                    <td>{{ $request->talent->user->location }}</td>
                </tr>
                @endif
                @if($request->talent->user->portfolio_url)
                <tr>
                    <th>Portfolio</th>
                    <td>{{ $request->talent->user->portfolio_url }}</td>
                </tr>
                @endif
                @if(isset($request->talent_skills) && count($request->talent_skills) > 0)
                <tr>
                    <th>Skills</th>
                    <td>
                        @foreach($request->talent_skills as $skill)
                            {{ is_array($skill) ? ($skill['skill_name'] ?? $skill['name'] ?? 'Unknown') : (is_string($skill) ? $skill : 'Unknown') }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                </tr>
                @else
                <tr>
                    <th>Skills</th>
                    <td><span>No skills listed</span></td>
                </tr>
                @endif
                @if($request->talent->user->talent_bio)
                <tr>
                    <th>Bio</th>
                    <td>{{ $request->talent->user->talent_bio }}</td>
                </tr>
                @endif
            </table>

            <div class="section-title">Project Details</div>
            <table>
                @if($request->project_description)
                <tr>
                    <th width="20%">Description</th>
                    <td>{{ $request->project_description }}</td>
                </tr>
                @endif
                @if($request->budget_range)
                <tr>
                    <th>Rentang Anggaran</th>
                    <td>{{ $request->budget_range }}</td>
                </tr>
                @endif
                @if($request->project_duration)
                <tr>
                    <th>Durasi</th>
                    <td>{{ preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) }}</td>
                </tr>
                @endif
                @if($request->requirements)
                <tr>
                    <th>Requirements</th>
                    <td>{{ $request->requirements }}</td>
                </tr>
                @endif
                @if($request->project_start_date)
                <tr>
                    <th>Project Start Date</th>
                    <td>{{ \Carbon\Carbon::parse($request->project_start_date)->format('d M Y') }}</td>
                </tr>
                @endif
                @if($request->project_end_date)
                <tr>
                    <th>Project End Date</th>
                    <td>{{ \Carbon\Carbon::parse($request->project_end_date)->format('d M Y') }}</td>
                </tr>
                @endif
            </table>

            <div class="section-title">Request Timeline</div>
            <table>
                <tr>
                    <th width="20%">Submitted</th>
                    <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @if($request->approved_at)
                <tr>
                    <th>Approved</th>
                    <td>{{ \Carbon\Carbon::parse($request->approved_at)->format('d M Y, H:i') }}</td>
                </tr>
                @endif
                @if($request->both_parties_accepted)
                <tr>
                    <th>Both Parties Accepted</th>
                    <td>Yes</td>
                </tr>
                @endif
                <tr>
                    <th>Current Status</th>
                    <td>{{ ucfirst($request->status) }}</td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td>{{ $request->updated_at->format('d M Y, H:i') }}</td>
                </tr>
            </table>
        </div>
        @endforeach
    @else
        <div class="no-data">
            <h3>No Talent Requests Found</h3>
            <p>You haven't made any talent requests yet.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Talent Scouting System</p>
        <p>© {{ date('Y') }} WebPelatihan. All rights reserved.</p>
    </div>
</body>
</html>
