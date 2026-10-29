<x-mail-layout judul="Penugasan Peserta Bimbingan Baru">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->pembimbing?->user?->name ?? 'Pembimbing' }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Anda ditugaskan sebagai pembimbing lapangan untuk peserta prakerin berikut:
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;font-size:13px;color:#475569;line-height:1.9;">
                <strong>{{ $peserta->nama_lengkap }}</strong><br>
                Asal instansi: {{ $peserta->instansi_pendidikan }}<br>
                Jurusan: {{ $peserta->jurusan }} ({{ $peserta->jenjang }})<br>
                Divisi penempatan: <strong>{{ $peserta->divisi_penempatan ?? '-' }}</strong><br>
                Periode: {{ $peserta->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $peserta->tanggal_selesai?->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Mohon memberikan bimbingan dan melakukan penilaian terhadap peserta tersebut melalui menu
        <em>Peserta Bimbingan</em> pada sistem.
    </p>

    <p style="margin:0;">
        <a href="{{ route('pembimbing.dashboard') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Buka Menu Bimbingan
        </a>
    </p>
</x-mail-layout>
