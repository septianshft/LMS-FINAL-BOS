<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penugasan Saya</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #4f46e5;
            font-size: 24px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .header h2 {
            color: #666;
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }

        .meta-info {
            margin-bottom: 25px;
            font-size: 11px;
            color: #666;
        }

        .meta-info div {
            margin-bottom: 5px;
        }

        .stats-summary {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #4f46e5;
        }

        .stats-summary h3 {
            margin: 0 0 10px 0;
            color: #4f46e5;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #eef2ff;
            color: #4f46e5;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card.pending { background: #fef3c7; color: #d97706; }
        .stat-card.accepted { background: #d1fae5; color: #059669; }
        .stat-card.completed { background: #dbeafe; color: #3b82f6; }
        .stat-card.declined { background: #fee2e2; color: #dc2626; }

        .stat-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        table th:nth-child(1) { width: 4%; }
        table th:nth-child(2) { width: 22%; }
        table th:nth-child(3) { width: 18%; }
        table th:nth-child(4) { width: 12%; }
        table th:nth-child(5) { width: 12%; }
        table th:nth-child(6) { width: 12%; }
        table th:nth-child(7) { width: 10%; }
        table th:nth-child(8) { width: 10%; }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tr:hover {
            background-color: #f3f4f6;
        }

        .status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            color: white;
        }

        .status.pending {
            background-color: #d97706;
        }

        .status.accepted {
            background-color: #059669;
        }

        .status.completed {
            background-color: #3b82f6;
        }

        .status.declined {
            background-color: #dc2626;
        }

        .budget {
            font-weight: bold;
            color: #059669;
        }

        .project-title {
            font-weight: bold;
            color: #1f2937;
        }

        .recruiter-name {
            color: #6b7280;
            font-style: italic;
        }

        .date {
            color: #6b7280;
            font-size: 9px;
        }

        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 40px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penugasan Proyek</h1>
        <h2>{{ $user->name }}</h2>
    </div>

    <div class="meta-info">
        <div><strong>Nama:</strong> {{ $user->name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Tanggal Ekspor:</strong> {{ now()->format('d F Y, H:i') }} WIB</div>
        <div><strong>Filter Status:</strong> 
            @if($filterStatus === 'all')
                Semua Status
            @else
                {{ ucfirst($filterStatus) }}
            @endif
        </div>
        <div><strong>Total Penugasan:</strong> {{ $assignments->count() }} dari {{ $stats['total'] }} penugasan</div>
    </div>

    <div class="stats-summary">
        <h3>Ringkasan Statistik Penugasan</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Penugasan</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Menunggu</div>
            </div>
            <div class="stat-card accepted">
                <div class="stat-number">{{ $stats['accepted'] }}</div>
                <div class="stat-label">Diterima</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number">{{ $stats['completed'] }}</div>
                <div class="stat-label">Selesai</div>
            </div>
            <div class="stat-card declined">
                <div class="stat-number">{{ $stats['declined'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>

    @if($assignments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Proyek</th>
                    <th>Recruiter</th>
                    <th>Budget</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Tanggal Penugasan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $index => $assignment)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <div class="project-title">{{ $assignment->project->title }}</div>
                            <div style="font-size: 8px; color: #6b7280; margin-top: 2px;">
                                {{ Str::limit($assignment->project->description, 80) }}
                            </div>
                        </td>
                        <td>
                            <div class="recruiter-name">{{ $assignment->project->recruiter->user->name }}</div>
                            <div style="font-size: 8px; color: #9ca3af;">{{ $assignment->project->recruiter->company_name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="budget">Rp {{ number_format($assignment->project->budget, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="date">{{ $assignment->project->start_date ? \Carbon\Carbon::parse($assignment->project->start_date)->format('d/m/Y') : 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="date">{{ $assignment->project->end_date ? \Carbon\Carbon::parse($assignment->project->end_date)->format('d/m/Y') : 'N/A' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="status {{ $assignment->status }}">
                                @switch($assignment->status)
                                    @case('pending')
                                        Menunggu
                                        @break
                                    @case('accepted')
                                        Diterima
                                        @break
                                    @case('completed')
                                        Selesai
                                        @break
                                    @case('declined')
                                        Ditolak
                                        @break
                                    @default
                                        {{ ucfirst($assignment->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <div class="date">{{ $assignment->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada penugasan yang ditemukan untuk filter yang dipilih.</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem pada {{ now()->format('d F Y, H:i') }} WIB</p>
        <p>© {{ date('Y') }} WebPelatihan - Sistem Manajemen Talent</p>
    </div>
</body>
</html>