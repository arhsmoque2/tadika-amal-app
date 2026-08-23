<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Sijil Penghargaan - {{ $student->name }}</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            color: #1e293b;
            margin: 0;
            padding: 30px;
            background-color: #ffffff;
            text-align: center;
        }
        .cert-container {
            border: 6px double #059669;
            padding: 40px;
            border-radius: 12px;
            background-color: #fcfdfd;
        }
        .school-name {
            font-size: 22px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .cert-title {
            font-size: 28px;
            font-weight: bold;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 24px;
            margin-bottom: 24px;
        }
        .cert-body {
            font-size: 15px;
            line-height: 1.8;
            color: #334155;
            margin-bottom: 24px;
        }
        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 2px solid #059669;
            display: inline-block;
            padding: 4px 24px;
            margin: 12px 0;
        }
        .award-reason {
            font-size: 16px;
            font-style: italic;
            color: #047857;
            margin-top: 12px;
        }
        .signatures-table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            font-family: 'Helvetica Neue', sans-serif;
            font-size: 12px;
        }
        .sign-line {
            width: 200px;
            border-bottom: 1px solid #64748b;
            margin: 0 auto 6px auto;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="school-name">{{ $school?->name ?: 'TADIKA AMAL' }}</div>
        <div style="font-size: 13px; color: #64748b; text-transform: uppercase;">Pendidikan Awal Kanak-Kanak Bersepadu</div>

        <div class="cert-title">SIJIL PENGHARGAAN</div>

        <div class="cert-body">
            Dengan sukacitanya diperakui bahawa
            <br>
            <div class="student-name">{{ $student->name }}</div>
            <br>
            MyKid: {{ $student->mykid ?: '-' }} &bull; Kelas: {{ $student->cohort?->name ?: 'Prasekolah' }}
            <br><br>
            Telah dianugerahkan
            <div class="award-reason">{{ $awardTitle }}</div>
            bagi Sesi Persekolahan {{ $academicYear }}.
        </div>

        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sign-line"></div>
                    <strong>Guru Kelas</strong><br>
                    {{ $student->cohort?->name ?: 'Tadika Amal' }}
                </td>
                <td>
                    <div class="sign-line"></div>
                    <strong>Guru Besar / Pengurus</strong><br>
                    {{ $school?->name ?: 'Tadika Amal' }}
                </td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; font-size: 10px; color: #94a3b8;">
            Tarikh Dikeluarkan: {{ $issueDate }} &bull; No Siri: TA-CERT-{{ date('Y') }}-{{ str_pad((string) $student->id, 4, '0', STR_PAD_LEFT) }}
        </div>
    </div>
</body>
</html>
