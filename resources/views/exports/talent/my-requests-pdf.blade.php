<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Saya</title>
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
            grid-template-columns: repeat(4, 1fr);
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
        .stat-card.rejected { background: #fee2e2; color: #dc2626; }

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
        table th:nth-child(3) { width: 13%; }
        table th:nth-child(4) { width: 13%; }
        table th:nth-child(5) { width: 10%; }
        table th:nth-child(6) { width: 8%; }
        table th:nth-child(7) { width: 8%; }
        table th:nth-child(8) { width: 11%; }
        table th:nth-child(9) { width: 11%; }

        th {
            background: #4f46e5;
            color: white;
            text-align: left;
            padding: 10px;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-accepted {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-completed {
            background-color: #dbeafe;
            color: #3b82f6;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Permintaan Saya</h1>
        <h2>Laporan Permintaan Kolaborasi Talent</h2>
    </div>

    <div class="meta-info">
        <div><strong>Talent:</strong> {{ $user->name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Tanggal Laporan:</strong> {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</div>
        <div><strong>Total Permintaan:</strong> {{ $requests->count() }}</div>
        <div><strong>Proyek Aktif:</strong> {{ $requests->filter(function($request) { return in_array($request->getDisplayStatus(), ['pending', 'accepted']); })->count() }}</div>
        <div><strong>Proyek Berakhir:</strong> {{ $requests->filter(function($request) { return $request->getDisplayStatus() === 'completed'; })->count() }}</div>
    </div>

    <div class="stats-summary">
        <h3>Ringkasan Status</h3>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Permintaan</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Menunggu</div>
            </div>
            <div class="stat-card accepted">
                <div class="stat-number">{{ $stats['accepted'] }}</div>
                <div class="stat-label">Diterima</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul Proyek</th>
                <th>Recruiter</th>
                <th>Perusahaan</th>
                <th>Anggaran</th>
                <th>Durasi</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
                <th>Tanggal Berakhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $index => $request)
                @php
                    $displayStatus = $request->getDisplayStatus();
                    $statusClass = '';
                    
                    switch($displayStatus) {
                        case 'pending':
                            $statusClass = 'status-pending';
                            $statusText = 'Menunggu';
                            break;
                        case 'accepted':
                            $statusClass = 'status-accepted';
                            $statusText = 'Diterima';
                            break;
                        case 'completed':
                            $statusClass = 'status-completed';
                            $statusText = 'Selesai';
                            break;
                        case 'rejected':
                            $statusClass = 'status-rejected';
                            $statusText = 'Ditolak';
                            break;
                        default:
                            $statusClass = '';
                            $statusText = ucfirst($displayStatus);
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $request->project_title }}</td>
                    <td>{{ $request->recruiter->user->name }}</td>
                    <td>{{ $request->recruiter->user->pekerjaan ?? 'Tidak disebutkan' }}</td>
                    <td>{{ $request->budget_range }}</td>
                    <td>{{ $request->project_duration }}</td>
                    <td><span class="status-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                    <td>{{ $request->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ $request->project_end_date ? $request->project_end_date->locale('id')->translatedFormat('d M Y') : 'Belum ditentukan' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</p>
        <p>© {{ date('Y') }} Platform Pelatihan - Semua hak dilindungi</p>
    </div>
</body>
</html>