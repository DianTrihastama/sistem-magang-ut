<x-mail-layout judul="Nilai Peserta Sudah Diinput">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>Admin</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Penilaian pembimbing untuk peserta berikut telah tersimpan dan nilai akhir telah dihitung ulang.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;font-size:13px;color:#475569;line-height:1.9;">
                Peserta: <strong>{{ $peserta->nama_lengkap }}</strong><br>
                Pembimbing: {{ $peserta->pembimbing?->user?->name ?? '-' }}<br>
                Rata-rata penilaian: <strong>{{ number_format($nilaiAkhir->nilai_pembimbing ?? 0, 2) }}</strong><br>
                Nilai kehadiran: <strong>{{ number_format($nilaiAkhir->nilai_kehadiran ?? 0, 2) }}</strong><br>
                Nilai akhir: <strong style="color:#111;">{{ $nilaiAkhir->nilai_akhir !== null ? number_format($nilaiAkhir->nilai_akhir, 2) : '-' }}</strong>
                &mdash; Predikat: <strong>{{ $nilaiAkhir->predikat ?? '-' }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Setelah periode magang berakhir, sertifikat dapat diterbitkan melalui halaman detail peserta.
    </p>

    <p style="margin:0;">
        <a href="{{ route('admin.pendaftar.show', $peserta) }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Lihat Detail Peserta
        </a>
    </p>
</x-mail-layout>
