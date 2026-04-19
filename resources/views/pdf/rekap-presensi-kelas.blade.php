<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 30px 35px;
            color: #222;
            line-height: 1.4;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2.5px solid #1a1a2e;
            padding-bottom: 12px;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a2e;
        }
        .header-sub {
            font-size: 12px;
            color: #444;
            margin-top: 4px;
        }
        .header-periode {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }

        /* ===== INFO BOX ===== */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-item {
            margin-bottom: 3px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
        }

        /* ===== TABLE ===== */
        table.rekap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.rekap thead tr {
            background-color: #1a1a2e;
            color: #ffffff;
        }
        table.rekap th {
            padding: 9px 7px;
            text-align: center;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1a1a2e;
        }
        table.rekap th.left {
            text-align: left;
        }
        table.rekap td {
            padding: 7px;
            border: 1px solid #d0d0d0;
            text-align: center;
            vertical-align: middle;
        }
        table.rekap td.left {
            text-align: left;
        }
        table.rekap tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        table.rekap tbody tr:hover {
            background-color: #eef2ff;
        }

        /* ===== STATUS COLORS ===== */
        .hadir  { color: #166534; font-weight: bold; }
        .izin   { color: #854d0e; font-weight: bold; }
        .sakit  { color: #075985; font-weight: bold; }
        .alfa   { color: #991b1b; font-weight: bold; }
        .total  { font-weight: bold; color: #1a1a2e; }

        /* ===== PERSENTASE ===== */
        .persen-tinggi  { color: #166534; font-weight: bold; }
        .persen-sedang  { color: #854d0e; font-weight: bold; }
        .persen-rendah  { color: #991b1b; font-weight: bold; }

        /* ===== LEGEND ===== */
        .legend {
            margin-top: 16px;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .legend span {
            margin-right: 18px;
        }

        /* ===== SIGNATURE ===== */
        .signature-area {
            margin-top: 30px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 180px;
        }
        .signature-line {
            margin-top: 55px;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-weight: bold;
        }

        /* ===== FOOTER ===== */
        .footer {
            font-size: 10px;
            color: #888;
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #e5e5e5;
            padding-top: 8px;
        }

        /* ===== SUMMARY BOX ===== */
        .summary-box {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: #f8fafc;
        }
        .summary-cell {
            display: table-cell;
            text-align: center;
            padding: 8px 12px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .summary-cell:last-child { border-right: none; }
        .summary-cell .val {
            font-size: 16px;
            font-weight: bold;
        }
        .summary-cell .lbl {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-title">Rekapitulasi Presensi Santri</div>
        <div class="header-sub">{{ $class->class_name }}</div>
        <div class="header-periode">Periode: {{ $periode }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y') }}</div>
    </div>

    {{-- INFO --}}
    <div class="info-row">
        <div class="info-cell">
            <div class="info-item">
                <span class="info-label">Kelas</span>: {{ $class->class_name }}
            </div>
            <div class="info-item">
                <span class="info-label">Total Santri</span>: {{ $totalSantri }} santri
            </div>
        </div>
        <div class="info-cell">
            <div class="info-item">
                <span class="info-label">Guru Pengajar</span>: {{ $teacher?->user?->name ?? '-' }}
            </div>
            <div class="info-item">
                <span class="info-label">Tahun Akademik</span>: {{ $periode }}
            </div>
        </div>
    </div>

    {{-- SUMMARY TOTALS --}}
    @php
        $totalHadir  = $rekapData->sum('hadir');
        $totalIzin   = $rekapData->sum('izin');
        $totalSakit  = $rekapData->sum('sakit');
        $totalAlfa   = $rekapData->sum('alfa');
        $totalPertemuan = $rekapData->sum('total');
        $avgPersen   = $rekapData->count() > 0
            ? round($rekapData->avg('persen'), 1)
            : 0;
    @endphp
    <div class="summary-box">
        <div class="summary-cell">
            <div class="val">{{ $totalSantri }}</div>
            <div class="lbl">Total Santri</div>
        </div>
        <div class="summary-cell">
            <div class="val hadir">{{ $totalHadir }}</div>
            <div class="lbl">Total Hadir</div>
        </div>
        <div class="summary-cell">
            <div class="val izin">{{ $totalIzin }}</div>
            <div class="lbl">Total Izin</div>
        </div>
        <div class="summary-cell">
            <div class="val sakit">{{ $totalSakit }}</div>
            <div class="lbl">Total Sakit</div>
        </div>
        <div class="summary-cell">
            <div class="val alfa">{{ $totalAlfa }}</div>
            <div class="lbl">Total Alfa</div>
        </div>
        <div class="summary-cell">
            <div class="val {{ $avgPersen >= 80 ? 'persen-tinggi' : ($avgPersen >= 60 ? 'persen-sedang' : 'persen-rendah') }}">
                {{ $avgPersen }}%
            </div>
            <div class="lbl">Rata-rata Hadir</div>
        </div>
    </div>

    {{-- TABEL REKAP PER SANTRI --}}
    <table class="rekap">
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th class="left" style="width: 32%">Nama Santri</th>
                <th style="width: 10%">Pertemuan</th>
                <th style="width: 10%">Hadir</th>
                <th style="width: 10%">Izin</th>
                <th style="width: 10%">Sakit</th>
                <th style="width: 10%">Alfa</th>
                <th style="width: 14%">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="left">{{ $row['nama'] }}</td>
                <td class="total">{{ $row['total'] }}</td>
                <td class="hadir">{{ $row['hadir'] }}</td>
                <td class="izin">{{ $row['izin'] }}</td>
                <td class="sakit">{{ $row['sakit'] }}</td>
                <td class="alfa">{{ $row['alfa'] }}</td>
                <td class="{{ $row['persen'] >= 80 ? 'persen-tinggi' : ($row['persen'] >= 60 ? 'persen-sedang' : 'persen-rendah') }}">
                    {{ $row['persen'] }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#999; padding: 20px;">
                    Belum ada data presensi untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LEGENDA --}}
    <div class="legend">
        <span><strong>Keterangan % Kehadiran:</strong></span>
        <span class="persen-tinggi">■ ≥ 80% : Baik</span>
        <span class="persen-sedang">■ 60–79% : Cukup</span>
        <span class="persen-rendah">■ &lt; 60% : Perlu Perhatian</span>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signature-area">
        <div class="signature-box">
            <div>Guru Pengajar,</div>
            <div class="signature-line">{{ $teacher?->user?->name ?? '.............................' }}</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem Informasi Darut Tafsir &nbsp;|&nbsp; {{ now()->format('d M Y, H:i') }} WIB
    </div>

</body>
</html>
