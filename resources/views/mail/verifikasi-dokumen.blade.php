<x-mail-layout judul="{{ $permintaanBaru ? 'Permintaan Melengkapi Dokumen Tahap 3' : 'Hasil Verifikasi Dokumen Tahap 3' }}">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->nama_lengkap }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        @if($permintaanBaru)
            Beberapa dokumen tahap 3 Anda belum dapat kami setujui. Mohon periksa hasil verifikasi berikut:
        @else
            Berikut hasil verifikasi dokumen tahap 3 Anda:
        @endif
    </p>

    @if(count($dokumen))
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
            <tr style="background:#f8fafc;">
                <td style="padding:10px 14px;font-size:12px;font-weight:bold;color:#475569;border-bottom:1px solid #e2e8f0;">Dokumen</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:bold;color:#475569;border-bottom:1px solid #e2e8f0;">Status</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:bold;color:#475569;border-bottom:1px solid #e2e8f0;">Catatan</td>
            </tr>
            @foreach($dokumen as $d)
                <tr>
                    <td style="padding:10px 14px;font-size:13px;color:#334155;border-bottom:1px solid #f1f5f9;">{{ $d->labelJenis() }}</td>
                    <td style="padding:10px 14px;font-size:13px;border-bottom:1px solid #f1f5f9;">
                        @if($d->status_verifikasi === 'valid')
                            <span style="color:#059669;font-weight:bold;">Valid</span>
                        @elseif($d->status_verifikasi === 'tidak_valid')
                            <span style="color:#e11d48;font-weight:bold;">Tidak Valid</span>
                        @else
                            <span style="color:#64748b;">Menunggu</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;font-size:12.5px;color:#475569;border-bottom:1px solid #f1f5f9;">{{ $d->catatan ?: '-' }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if($permintaanBaru)
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Mohon mengunggah ulang dokumen yang tidak valid melalui menu <em>Dokumen Tahap 3</em>, kemudian
            konfirmasi kembali ke pihak HRGA.
        </p>
    @else
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Status tahap Anda saat ini: <strong>{{ $peserta->statusLabel() }}</strong>.
        </p>
    @endif

    <p style="margin:0;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Buka Sistem
        </a>
    </p>
</x-mail-layout>
