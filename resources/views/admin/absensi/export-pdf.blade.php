<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; padding: 24px; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        h2 { font-size: 12px; font-weight: normal; color: #444; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #FDC300; color: #111; font-size: 10px; text-transform: uppercase; }
        td.nilai { text-align: center; }
        .meta { margin-bottom: 16px; font-size: 11px; color: #333; }
        .footer { margin-top: 24px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>REKAPITULASI ABSENSI PRAKERIN</h1>
    <h2>PT United Tractors Samarinda &mdash; {{ $peserta ? $peserta->nama_lengkap : 'Seluruh Peserta' }} {{ $bulan ? '- ' . \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') : '' }}</h2>

    <div class="meta">
        Dicetak: {{ now()->format('d/m/Y H:i') }}
        @if($nilaiKehadiran)
            &middot; Total hari kerja: {{ $nilaiKehadiran->total_hari_kerja }}
            &middot; Nilai kehadiran: <strong>{{ number_format($nilaiKehadiran->nilai, 2) }}</strong>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:14%">Tanggal</th>
                <th style="width:26%">Peserta</th>
                <th style="width:10%">Jam Masuk</th>
                <th style="width:12%">Status</th>
                <th style="width:23%">Keterangan</th>
                <th style="width:10%">Persetujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensi as $index => $row)
                <tr>
                    <td class="nilai">{{ $index + 1 }}</td>
                    <td>{{ $row->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $row->peserta->nama_lengkap }}</td>
                    <td class="nilai">{{ $row->jam_masuk ?? '-' }}</td>
                    <td>{{ \App\Models\Absensi::STATUS_LABELS[$row->status] }}</td>
                    <td>{{ $row->keterangan ?: '-' }}</td>
                    <td class="nilai">
                        @if($row->disetujui === true) Disetujui
                        @elseif($row->disetujui === false) Ditolak
                        @else - @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center">Tidak ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen dihasilkan otomatis oleh Sistem Pendaftaran &amp; Manajemen Magang PT United Tractors Samarinda.
    </div>
</body>
</html>
