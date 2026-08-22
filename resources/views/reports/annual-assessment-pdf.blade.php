<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perkembangan Murid - {{ $student->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            color: #1e3a8a;
        }
        .header h2 {
            font-size: 13px;
            margin: 4px 0 0 0;
            font-weight: 500;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 18%;
            color: #4b5563;
        }
        .val {
            width: 32%;
            border-bottom: 1px dotted #9ca3af;
        }
        .domain-title {
            background-color: #f3f4f6;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #1e40af;
            border-left: 4px solid #2563eb;
            margin-top: 12px;
            margin-bottom: 6px;
        }
        .skill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .skill-table th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
        }
        .skill-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            font-size: 10px;
        }
        .badge-tm {
            color: #065f46;
            background-color: #d1fae5;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .badge-sm {
            color: #92400e;
            background-color: #fef3c7;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .badge-bm {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            width: 33%;
            text-align: center;
            vertical-align: bottom;
            height: 70px;
        }
        .sign-line {
            border-top: 1px solid #374151;
            margin: 0 20px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>{{ $school->name ?? 'TADIKA ISLAM AMAL' }}</h1>
    <h2>REKOD PENILAIAN PERKEMBANGAN MURID PRASEKOLAH (KSPK)</h2>
    <div style="font-size: 10px; color: #6b7280; margin-top: 2px;">
        Sesi Persekolahan: {{ $academicYear }} | Tempoh: {{ $period }}
    </div>
</div>

<table class="meta-table">
    <tr>
        <td class="label">Nama Murid:</td>
        <td class="val"><strong>{{ strtoupper($student->name) }}</strong></td>
        <td class="label">No. MyKid:</td>
        <td class="val">{{ $student->mykid ?: '-' }}</td>
    </tr>
    <tr>
        <td class="label">Kelas / Cohort:</td>
        <td class="val">{{ $cohort->name ?? '-' }}</td>
        <td class="label">Guru Kelas:</td>
        <td class="val">{{ $cohort->teacher->full_name ?? '-' }}</td>
    </tr>
    <tr>
        <td class="label">Tarikh Lahir:</td>
        <td class="val">{{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->format('d/m/Y') : '-' }}</td>
        <td class="label">Jantina:</td>
        <td class="val">{{ $student->gender }}</td>
    </tr>
</table>

@foreach($skillsByDomain as $domain => $domainSkills)
    <div class="domain-title">{{ strtoupper($domain) }}</div>
    <table class="skill-table">
        <thead>
            <tr>
                <th style="width: 12%;">Kod</th>
                <th style="width: 58%;">Indikator & Standard Pembelajaran</th>
                <th style="width: 30%; text-align: center;">Tahap Penguasaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($domainSkills as $skill)
                @php
                    $eval = $evaluations->get($skill->id);
                    $scale = $eval?->skillScale;
                @endphp
                <tr>
                    <td style="font-weight: bold; font-family: monospace;">{{ $skill->code ?: '-' }}</td>
                    <td>
                        <strong>{{ $skill->name }}</strong>
                        @if($skill->description)
                            <div style="color: #6b7280; font-size: 9px;">{{ $skill->description }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($scale)
                            @if($scale->value == 3)
                                <span class="badge-tm">TM - Menguasai</span>
                            @elseif($scale->value == 2)
                                <span class="badge-sm">SM - Sedang Menguasai</span>
                            @else
                                <span class="badge-bm">BM - Belum Menguasai</span>
                            @endif
                        @else
                            <span style="color: #9ca3af;">Belum dinilai</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

<div style="margin-top: 15px; padding: 10px; border: 1px solid #e5e7eb; background-color: #fafafa; border-radius: 4px;">
    <strong>Ulasan Guru Kelas:</strong>
    <p style="margin: 4px 0 0 0; font-style: italic; min-height: 35px; color: #374151;">
        {{ $reportMeta->teacher_comments ?? 'Murid menunjukkan minat yang sangat baik dalam aktiviti bilik darjah dan sentiasa bersemangat dalam pembelajaran harian.' }}
    </p>
</div>

<table class="signatures">
    <tr>
        <td>
            <div class="sign-line">Guru Kelas</div>
        </td>
        <td>
            <div class="sign-line">Guru Besar / Pengetua</div>
        </td>
        <td>
            <div class="sign-line">Tandatangan Ibu Bapa / Penjaga</div>
        </td>
    </tr>
</table>

<div style="font-size: 8px; color: #9ca3af; text-align: right; margin-top: 12px;">
    Dicetak secara automatik pada: {{ $printedDate }} | Tadika Amal Management Platform
</div>

</body>
</html>
