<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Resit Rasmi Bayaran Yuran - {{ $receiptNo }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-title {
            font-size: 15px;
            font-weight: bold;
            color: #065f46;
            text-transform: uppercase;
        }
        .school-sub {
            font-size: 9px;
            color: #4b5563;
        }
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            color: #1f2937;
        }
        .receipt-badge {
            background-color: #d1fae5;
            color: #065f46;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 3px 6px;
            font-size: 10px;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
            width: 18%;
        }
        .val {
            width: 32%;
            border-bottom: 1px dotted #9ca3af;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .items-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 10px;
            text-align: left;
        }
        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 10px;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f9fafb;
            font-size: 11px;
        }
        .lhdn-box {
            border: 1px dashed #059669;
            background-color: #f0fdf4;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 9px;
            color: #065f46;
            margin-top: 8px;
        }
        .footer-table {
            width: 100%;
            margin-top: 15px;
        }
        .footer-table td {
            width: 50%;
            vertical-align: bottom;
            font-size: 9px;
        }
        .sign-box {
            border-top: 1px solid #4b5563;
            width: 180px;
            text-align: center;
            padding-top: 4px;
            font-weight: bold;
            font-size: 9px;
        }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td style="width: 60%;">
            <div class="school-title">{{ $school->name ?? 'TADIKA ISLAM AMAL BESTARI' }}</div>
            <div class="school-sub">{{ $school->address ?? 'No 12, Jalan Bestari 2, Bandar Baru Bangi, 43650 Selangor' }}</div>
            <div class="school-sub">Tel: {{ $school->phone ?? '03-89210001' }} | Kod Tadika: {{ $school->code ?? 'TADIKA-AMAL-01' }}</div>
        </td>
        <td style="width: 40%; text-align: right;">
            <div class="receipt-title">RESIT RASMI BAYARAN</div>
            <div style="font-size: 11px; font-weight: bold; color: #059669; margin-top: 2px;">{{ $receiptNo }}</div>
            <div class="receipt-badge" style="margin-top: 4px;">LUNAS / TELAH DIBAYAR</div>
        </td>
    </tr>
</table>

<table class="meta-table">
    <tr>
        <td class="label">Diterima Daripada:</td>
        <td class="val"><strong>{{ strtoupper($student->guardian_name ?: 'Ibu Bapa / Penjaga') }}</strong></td>
        <td class="label">Tarikh Bayaran:</td>
        <td class="val">{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('d/m/Y') : date('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="label">Nama Murid:</td>
        <td class="val">{{ strtoupper($student->name ?? '-') }}</td>
        <td class="label">Kaedah Bayaran:</td>
        <td class="val">{{ strtoupper(str_replace('_', ' ', $invoice->payment_method ?: 'FPX ONLINE')) }}</td>
    </tr>
    <tr>
        <td class="label">No. MyKid Murid:</td>
        <td class="val">{{ $student->mykid ?: '-' }}</td>
        <td class="label">Kelas / Sesi:</td>
        <td class="val">{{ $cohort->name ?? '-' }} ({{ $invoice->academic_year }})</td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 10%; text-align: center;">Bil</th>
            <th style="width: 65%;">Keterangan Pembayaran Yuran</th>
            <th style="width: 25%; text-align: right;">Jumlah (RM)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">1</td>
            <td>
                <strong>{{ $invoice->category_label }} - Bulan {{ $invoice->month }} ({{ $invoice->academic_year }})</strong>
                <div style="color: #6b7280; font-size: 8px;">No. Invois Asal: {{ $invoice->invoice_number }}</div>
            </td>
            <td style="text-align: right; font-weight: bold;">
                RM {{ number_format($invoice->amount, 2) }}
            </td>
        </tr>
        <tr class="total-row">
            <td colspan="2" style="text-align: right;">JUMLAH KESELURUHAN DITERIMA:</td>
            <td style="text-align: right; color: #065f46; font-size: 12px;">
                RM {{ number_format($invoice->amount, 2) }}
            </td>
        </tr>
    </tbody>
</table>

<div class="lhdn-box">
    <strong>📌 AKUAN PELEPASAN CUKAI PENDAPATAN LHDN (SEKSYEN 46(1)(r)):</strong>
    Resit ini adalah dokumen bukti perbelanjaan yuran taska / tadika berdaftar yang sah bagi tujuan tuntutan pelepasan cukai pendapatan individu sehingga RM3,000.00 di bawah Lembaga Hasil Dalam Negeri Malaysia (LHDN).
</div>

<table class="footer-table">
    <tr>
        <td>
            <div style="color: #6b7280; font-size: 8px;">
                Resit dijana secara digital pada: {{ $printedDate }}<br>
                Tandatangan fizikal tidak diperlukan bagi dokumen elektronik berkomputer.
            </div>
        </td>
        <td style="text-align: right;">
            <div class="sign-box" style="margin-left: auto;">
                Bendahari / Pengurus Tadika
            </div>
        </td>
    </tr>
</table>

</body>
</html>
