<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Taklimat Orientasi - {{ $school->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
        }
        .section-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px;
            margin-bottom: 14px;
        }
        .section-title {
            font-weight: bold;
            color: #047857;
            font-size: 13px;
            margin-bottom: 6px;
            border-left: 3px solid #059669;
            padding-left: 8px;
        }
        ul {
            margin: 6px 0;
            padding-left: 20px;
        }
        li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school->name }}</h1>
        <div style="font-size: 13px; color: #64748b;">Ringkasan Taklimat Orientasi Sesi Persekolahan {{ $academicYear }}</div>
    </div>

    <div class="section-box">
        <div class="section-title">1. Visi & Falsafah Tadika</div>
        <ul>
            <li>Membentuk sahsiah cemerlang berteraskan adab, ilmu, dan nilai Islam.</li>
            <li>Pelaksanaan Kurikulum Standard Prasekolah Kebangsaan (KSPK) bersepadu.</li>
            <li>Persekitaran pembelajaran yang selamat, kondusif, dan mesra kanak-kanak.</li>
        </ul>
    </div>

    <div class="section-box">
        <div class="section-title">2. Rutin Harian & Waktu Operasi</div>
        <ul>
            <li><strong>07:30 - 08:00 AM:</strong> Ketibaan murid & Saringan Kesihatan Pagi (Pintu Utama).</li>
            <li><strong>08:00 - 10:00 AM:</strong> Sesi Pembelajaran & Aktiviti KSPK / Al-Quran.</li>
            <li><strong>10:00 - 10:30 AM:</strong> Waktu Rehat & Kudapan Berkhasiat.</li>
            <li><strong>12:00 PM:</strong> Waktu Kepulangan & Imbasan Pengambilan Murid.</li>
        </ul>
    </div>

    <div class="section-box">
        <div class="section-title">3. Panduan Keselamatan & Kesihatan Ibu Bapa</div>
        <ul>
            <li>Murid yang mempunyai suhu $\ge 37.5^\circ\text{C}$ atau simptom HFMD/selesema dinasihatkan berehat di rumah.</li>
            <li>Pengambilan murid hanya dibenarkan oleh ibu bapa/penjaga berdaftar dengan kad pengenalan murid.</li>
        </ul>
    </div>
</body>
</html>
