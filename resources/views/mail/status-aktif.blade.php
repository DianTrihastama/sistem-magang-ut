<x-mail-layout judul="Status Aktif Magang &amp; Surat Balasan">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->nama_lengkap }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Selamat! Status Anda resmi <strong>Aktif Magang</strong> di PT United Tractors Samarinda.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;font-size:13px;color:#475569;line-height:1.9;">
                <strong>{{ $peserta->nama_lengkap }}</strong><br>
                Divisi penempatan: <strong>{{ $peserta->divisi_penempatan ?? '-' }}</strong><br>
                Pembimbing: <strong>{{ $peserta->pembimbing?->user?->name ?? '-' }}</strong><br>
                Periode: {{ $peserta->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $peserta->tanggal_selesai?->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Surat balasan penerimaan prakerin terlampir pada email ini (PDF). Mulai hari kerja pertama, silakan melakukan
        absensi harian melalui menu <em>Absensi</em> pada sistem.
    </p>

    <p style="margin:0;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Buka Sistem
        </a>
    </p>
</x-mail-layout>
