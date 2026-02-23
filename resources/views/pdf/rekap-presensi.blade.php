<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 40px;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .sub-title {
            font-size: 13px;
            color: #555;
        }

        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 180px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
            color: #222;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }

        table.data-table th {
            background: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .summary-table td {
            font-size: 14px;
            font-weight: bold;
        }

        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }

        .status-izin {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-sakit {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-alfa {
            background-color: #f8d7da;
            color: #721c24;
        }

        .detail-table td.status {
            font-weight: bold;
            font-size: 11px;
        }

        .detail-table td:first-child {
            text-align: left;
        }

        .detail-table td:nth-child(2),
        .detail-table td:nth-child(3) {
            text-align: left;
        }

        .bottom-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0 40px;
        }

        .footer {
            font-size: 11px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .signature {
            text-align: right;
            margin-bottom: 10px;
        }

        .signature div {
            margin-bottom: 60px;
        }

        .percentage {
            font-size: 11px;
            color: #666;
            font-weight: normal;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-title">Rekap Presensi Santri</div>
        <div class="sub-title">Periode: {{ $periode }}</div>
    </div>

    <!-- INFORMASI SANTRI -->
    <table class="info-table">
        <tr>
            <td><strong>Nama Santri</strong></td>
            <td>: {{ $student->student_name }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ optional($student->class)->class_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Total Pertemuan</strong></td>
            <td>: {{ $summary['total'] }} kali</td>
        </tr>
    </table>

    <!-- RINGKASAN KEHADIRAN -->
    <div class="section-title">Ringkasan Kehadiran</div>
    <table class="data-table summary-table">
        <thead>
            <tr>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alfa</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="status-hadir">
                    {{ $summary['hadir'] }}
                    @if($summary['total'] > 0)
                        <br><span class="percentage">({{ round($summary['hadir'] / $summary['total'] * 100, 1) }}%)</span>
                    @endif
                </td>
                <td class="status-izin">
                    {{ $summary['izin'] }}
                    @if($summary['total'] > 0)
                        <br><span class="percentage">({{ round($summary['izin'] / $summary['total'] * 100, 1) }}%)</span>
                    @endif
                </td>
                <td class="status-sakit">
                    {{ $summary['sakit'] }}
                    @if($summary['total'] > 0)
                        <br><span class="percentage">({{ round($summary['sakit'] / $summary['total'] * 100, 1) }}%)</span>
                    @endif
                </td>
                <td class="status-alfa">
                    {{ $summary['alfa'] }}
                    @if($summary['total'] > 0)
                        <br><span class="percentage">({{ round($summary['alfa'] / $summary['total'] * 100, 1) }}%)</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $summary['total'] }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- DETAIL KEHADIRAN -->
    @if($details->count() > 0)
    <div class="section-title">Detail Kehadiran</div>
    <table class="data-table detail-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Tanggal</th>
                <th style="width: 30%">Mata Pelajaran</th>
                <th style="width: 25%">Kelas</th>
                <th style="width: 20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="text-align: left;">{{ $detail['date'] }}</td>
                <td style="text-align: left;">{{ $detail['lesson'] }}</td>
                <td>{{ $detail['class'] }}</td>
                <td class="status status-{{ strtolower($detail['status']) }}">
                    {{ $detail['status'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- TANDA TANGAN & FOOTER (fixed di bawah) -->
    <div class="bottom-section">
        <div class="signature">
            <div><strong>Guru Pengajar</strong></div>
            <div style="margin-top: 60px;">
                <strong>{{ auth()->user()->name ?? '.........................' }}</strong>
            </div>
        </div>

        <div class="footer">
            Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

</body>
</html>
