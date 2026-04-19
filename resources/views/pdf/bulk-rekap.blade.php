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
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
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
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin: 25px 0 8px 0;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        table.data-table th {
            background: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 35px;
            font-size: 12px;
            text-align: right;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
        }

        .signature div {
            margin-bottom: 60px;
        }
    </style>
</head>
<body>

    @foreach($studentData as $index => $data)
        @php
            $student = $data['student'];
            $periode = $data['periode'];
        @endphp

        <!-- HEADER -->
        <div class="header">
            <div class="header-title">Rekap Nilai Hafalan Santri</div>
            <div class="sub-title">Periode {{ $periode }}</div>
        </div>

        <!-- INFORMASI SANTRI -->
        <table class="info-table">
            <tr>
                <td><strong>Nama Santri</strong></td>
                <td>: {{ $student->student_name }}</td>
            </tr>
            <tr>
                <td><strong>Kelas</strong></td>
                <td>: {{ optional($student->class)->class_name }}</td>
            </tr>
            <tr>
                <td><strong>Guru Pembimbing</strong></td>
                <td>: {{ optional($student->pembimbing->first())->user->name }}</td>
            </tr>
        </table>

        <!-- BAGIAN 1: TAHSINUL QIROAT -->
        <div class="section-title">Tahsinul Qiroat</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Makharijul Huruf</th>
                    <th>Shifatul Huruf</th>
                    <th>Ahkamul Qiroat</th>
                    <th>Ahkamul Waqfi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $student->avg_makharijul_huruf }}</td>
                    <td>{{ $student->avg_shifatul_huruf }}</td>
                    <td>{{ $student->avg_ahkamul_qiroat }}</td>
                    <td>{{ $student->avg_ahkamul_waqfi }}</td>
                </tr>
            </tbody>
        </table>

        <!-- BAGIAN 2: PEMAHAMAN TAFSIR -->
        <div class="section-title">Pemahaman Tafsir</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Qowaid Tafsir</th>
                    <th>Tarjamatul Ayat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $student->avg_qowaid_tafsir }}</td>
                    <td>{{ $student->avg_tarjamatul_ayat }}</td>
                </tr>
            </tbody>
        </table>

        <!-- BAGIAN 3: REKAP NILAI AKHIR -->
        <div class="section-title">Rekap Nilai Akhir</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nilai Rata-Rata</th>
                    <th>Total Setoran</th>
                    <th>Juz Terakhir</th>
                    <th>Surah Terakhir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $student->avg_nilai }}</td>
                    <td>{{ $student->total_setoran_6_bulan }}</td>
                    <td>{{ optional($student->latestMemorize)->juz }}</td>
                    <td>{{ optional(optional($student->latestMemorize)->surah)->surah_name }}</td>
                </tr>
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="signature">
            <div><strong>Guru Pembimbing</strong></div>
            <div style="margin-top: 60px;">
                <strong>{{ optional($student->pembimbing->first())->user->name }}</strong>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
