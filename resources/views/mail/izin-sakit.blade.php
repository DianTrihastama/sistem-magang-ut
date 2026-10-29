<x-mail-layout judul="Pengajuan {{ ucfirst($absensi->status) }} {{ $disetujui ? 'Disetujui' : 'Ditolak' }}">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->nama_lengkap }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Pengajuan kehadiran Anda telah diproses oleh pihak HRGA.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;font-size:13px;color:#475569;line-height:1.9;">
                Tanggal: <strong>{{ $absensi->tanggal->format('d/m/Y') }}</strong><br>
                Status diajukan: <strong>{{ \App\Models\Absensi::STATUS_LABELS[$absensi->status] }}</strong><br>
                Keterangan: {{ $absensi->keterangan ?: '-' }}<br>
                Keputusan:
                <strong style="color:{{ $disetujui ? '#059669' : '#e11d48' }};">{{ $disetujui ? 'Disetujui' : 'Ditolak' }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        @if($disetujui)
            Pengajuan Anda telah tercatat pada rekap kehadiran magang.
        @else
            Mohon menghubungi pihak HRGA bila Anda ingin mengajukan keterangan ulang.
        @endif
        Status terkini: <strong>{{ $peserta->statusLabel() }}</strong>.
    </p>

    <p style="margin:0;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Buka Sistem
        </a>
    </p>
</x-mail-layout>
