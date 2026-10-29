<x-mail-layout judul="Sertifikat Magang Sudah Terbit">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $peserta->nama_lengkap }}</strong>,
    </p>
    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Selamat! Sertifikat praktik kerja industri Anda telah diterbitkan oleh PT United Tractors Samarinda.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:12px;margin-bottom:16px;">
        <tr>
            <td style="padding:16px;background:#f8fafc;font-size:13px;color:#475569;line-height:1.9;">
                Nomor Sertifikat: <strong>{{ $sertifikat->nomor_sertifikat }}</strong><br>
                Tanggal Terbit: {{ $sertifikat->tanggal_terbit->format('d/m/Y') }}<br>
                Nilai Akhir: <strong>{{ $peserta->nilaiAkhir?->nilai_akhir !== null ? number_format($peserta->nilaiAkhir->nilai_akhir, 2) : '-' }}</strong>
                &mdash; Predikat: <strong>{{ $peserta->nilaiAkhir?->predikat ?? '-' }}</strong><br>
                Kode Verifikasi: <strong style="font-family:monospace;letter-spacing:1px;">{{ $sertifikat->kode_verifikasi }}</strong>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 14px;color:#334155;font-size:14px;line-height:1.7;">
        Sertifikat dapat diunduh melalui sistem (menu Dokumen Saya). Keaslian sertifikat dapat diverifikasi publik melalui tautan:
        <br><a href="{{ url('/verifikasi/' . $sertifikat->kode_verifikasi) }}" style="color:#0369a1;word-break:break-all;">
            {{ url('/verifikasi/' . $sertifikat->kode_verifikasi) }}
        </a>
    </p>

    <p style="margin:0;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Unduh Sertifikat
        </a>
    </p>
</x-mail-layout>
