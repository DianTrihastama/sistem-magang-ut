@php($label = match($keputusan) {
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak',
    default => 'Perlu Revisi Berkas',
})
<x-mail-layout judul="Hasil Seleksi Administrasi: {{ $label }}">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->nama_lengkap }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Hasil seleksi administrasi pendaftaran prakerin Anda di PT United Tractors Samarinda telah keluar.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;">
                <p style="margin:0 0 6px;color:#64748b;font-size:12px;">Keputusan</p>
                <p style="margin:0;font-size:16px;font-weight:bold;color:{{ $keputusan === 'diterima' ? '#059669' : ($keputusan === 'ditolak' ? '#e11d48' : '#b45309') }};">
                    {{ $label }}
                </p>
                @if($catatan)
                    <p style="margin:10px 0 0;color:#475569;font-size:13px;line-height:1.6;">Catatan admin: {{ $catatan }}</p>
                @endif
                <p style="margin:6px 0 0;color:#475569;font-size:13px;">Tahap saat ini: <strong>{{ $peserta->statusLabel() }}</strong></p>
            </td>
        </tr>
    </table>

    @if($keputusan === 'diterima')
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Selamat! Anda dinyatakan diterima. Silakan lanjutkan ke <strong>Tahap 3 - Pemenuhan Dokumen</strong>
            dengan mengunggah Kartu BPJS Kesehatan, Kartu Asuransi Kecelakaan Kerja, dan Surat Keterangan Sehat
            melalui menu <em>Dokumen Tahap 3</em>.
        </p>
    @elseif($keputusan === 'revisi')
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Mohon melakukan revisi berkas administrasi Anda sesuai catatan di atas, lalu hubungi pihak HRGA.
        </p>
    @else
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Mohon maaf, berkas Anda belum memenuhi persyaratan seleksi administrasi prakerin.
        </p>
    @endif

    <p style="margin:0;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Lihat Detail di Sistem
        </a>
    </p>
</x-mail-layout>
