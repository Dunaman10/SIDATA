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
            <td>: {{ $student->class->class_name }}</td>
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

    <!-- BAGIAN 4: RINCIAN SETORAN HAFALAN -->
    <div style="page-break-inside: avoid;">
        <div class="section-title">Riwayat Setoran (1 Semester Terakhir)</div>
        <div style="font-size: 9px; margin-top: -5px; margin-bottom: 8px; color: #555;">
            *Keterangan: M.H = Makharijul Huruf, S.H = Shifatul Huruf, A.Q = Ahkamul Qiroat, A.W = Ahkamul Waqfi, Q.T = Qowaid Tafsir, T.A = Tarjamatul Ayat
        </div>
        <table class="data-table" style="font-size: 10px;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 20px;">No</th>
                    <th rowspan="2">Tanggal</th>
                    <th rowspan="2">Juz</th>
                    <th rowspan="2">Surah</th>
                    <th rowspan="2">Ayat</th>
                    <th colspan="4">Tahsinul Qiroat</th>
                    <th colspan="2">Pemahaman Tafsir</th>
                    <th rowspan="2">Nilai<br>Akhir</th>
                </tr>
                <tr>
                    <th style="width: 25px;">M.H</th>
                    <th style="width: 25px;">S.H</th>
                    <th style="width: 25px;">A.Q</th>
                    <th style="width: 25px;">A.W</th>
                    <th style="width: 25px;">Q.T</th>
                    <th style="width: 25px;">T.A</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memorizes as $index => $memorize)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($memorize->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $memorize->juz }}</td>
                    <td>{{ optional($memorize->surah)->surah_name }}</td>
                    <td>{{ $memorize->from }} - {{ $memorize->to }}</td>
                    <td>{{ $memorize->makharijul_huruf ?? '-' }}</td>
                    <td>{{ $memorize->shifatul_huruf ?? '-' }}</td>
                    <td>{{ $memorize->ahkamul_qiroat ?? '-' }}</td>
                    <td>{{ $memorize->ahkamul_waqfi ?? '-' }}</td>
                    <td>{{ $memorize->qowaid_tafsir ?? '-' }}</td>
                    <td>{{ $memorize->tarjamatul_ayat ?? '-' }}</td>
                    <td><strong>{{ $memorize->nilai_avg ?? '-' }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="padding: 10px;">Belum ada riwayat setoran pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature" style="page-break-inside: avoid;">
        <div><strong>Guru Pembimbing</strong></div>
        <div style="margin-top: 60px;">
            <strong>{{ optional($student->pembimbing->first())->user->name }}</strong>
        </div>
    </div>

</body>
</html>
