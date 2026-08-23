<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Buletin Kesihatan Harian - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #059669;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
            color: #065f46;
        }
        .header h2 {
            font-size: 14px;
            margin: 4px 0 0 0;
            color: #475569;
            font-weight: 500;
        }
        .date-badge {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 8px;
            border: 1px solid #a7f3d0;
        }
        .metrics-grid {
            width: 100%;
            margin-bottom: 24px;
            border-collapse: collapse;
        }
        .metrics-grid td {
            width: 33.33%;
            padding: 12px;
            text-align: center;
        }
        .metric-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            background-color: #f8fafc;
        }
        .metric-num {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .metric-num.emerald { color: #059669; }
        .metric-num.rose { color: #e11d48; }
        .metric-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .table-section th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-section td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-normal { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #ffe4e6; color: #9f1239; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school->name }}</h1>
        <h2>BULETIN SARINGAN KESIHATAN & KESELAMATAN PAGI</h2>
        <div class="date-badge">{{ $date }}</div>
    </div>

    <table class="metrics-grid">
        <tr>
            <td>
                <div class="metric-card">
                    <div class="metric-num">{{ $totalChecked }}</div>
                    <div class="metric-label">Jumlah Murid Disaring</div>
                </div>
            </td>
            <td>
                <div class="metric-card">
                    <div class="metric-num emerald">{{ $normalCount }}</div>
                    <div class="metric-label">Suhu Normal & Sihat</div>
                </div>
            </td>
            <td>
                <div class="metric-card">
                    <div class="metric-num rose">{{ $flaggedCount }}</div>
                    <div class="metric-label">Perlu Perhatian / Kuarantin</div>
                </div>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 13px; color: #334155; margin-bottom: 8px; text-transform: uppercase;">Rekod Saringan Terperinci</h3>
    <table class="table-section">
        <thead>
            <tr>
                <th style="width: 5%;">Bil</th>
                <th style="width: 35%;">Nama Murid</th>
                <th style="width: 25%;">Kelas</th>
                <th style="width: 15%;">Suhu (°C)</th>
                <th style="width: 20%;">Status & Gejala HFMD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($screenings as $index => $screening)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 600;">{{ $screening->student?->name }}</td>
                    <td>{{ $screening->student?->cohort?->name ?: '-' }}</td>
                    <td>{{ number_format($screening->temperature, 1) }} °C</td>
                    <td>
                        @if($screening->temperature >= 37.5 || $screening->has_hfmd_symptoms)
                            <span class="badge badge-warning">Demam / Simptom</span>
                        @else
                            <span class="badge badge-normal">Normal</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 16px;">Tiada rekod saringan kesihatan bagi tarikh ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dijana secara automatik oleh Sistem Pengurusan Operasi Tadika Amal Apps &bull; JKM & KKM Compliance Gate
    </div>
</body>
</html>
