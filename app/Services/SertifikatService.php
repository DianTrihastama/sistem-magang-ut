<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\Sertifikat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertifikatService
{
    private const DISK = 'privat';

    /**
     * Generate sertifikat magang (PDF landscape A4) lengkap dengan QR verifikasi.
     */
    public function generate(Peserta $peserta, ?int $dibuatOleh = null): Sertifikat
    {
        $nilaiAkhir = $peserta->nilaiAkhir;

        abort_unless($nilaiAkhir && $nilaiAkhir->nilai_akhir !== null, 422, 'Nilai peserta belum diinput pembimbing.');

        $nomorService = app(NomorSuratService::class);
        $existing = $peserta->sertifikat;

        $nomorSertifikat = $existing?->nomor_sertifikat ?? $nomorService->nomorSertifikat();
        $kodeVerifikasi = $existing?->kode_verifikasi ?? NomorSuratService::kodeVerifikasi();
        $tanggalTerbit = $existing?->tanggal_terbit ?? now()->toDateString();

        $urlVerifikasi = url('/verifikasi/' . $kodeVerifikasi);

        // Gunakan format SVG agar tidak memerlukan ekstensi Imagick (hanya butuh GD/SVG lib).
        $qrSvg = QrCode::format('svg')->size(220)->margin(1)->generate($urlVerifikasi);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pdf = Pdf::loadView('pdf.sertifikat', [
            'peserta' => $peserta,
            'nilaiAkhir' => $nilaiAkhir,
            'nomorSertifikat' => $nomorSertifikat,
            'tanggalTerbit' => $tanggalTerbit,
            'kodeVerifikasi' => $kodeVerifikasi,
            'urlVerifikasi' => $urlVerifikasi,
            'qrDataUri' => $qrDataUri,
            'namaPic' => PengaturanService::teks('nama_pic', 'PIC Prakerin'),
            'jabatanPic' => PengaturanService::teks('jabatan_pic', 'HRGA'),
        ])->setPaper('a4', 'landscape');

        $namaFile = 'sertifikat-' . $peserta->id . '-' . time() . '.pdf';
        $path = 'sertifikat/' . $namaFile;

        Storage::disk(self::DISK)->put($path, $pdf->output());

        if ($existing && Storage::disk(self::DISK)->exists($existing->path_file)) {
            Storage::disk(self::DISK)->delete($existing->path_file);
        }

        return Sertifikat::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nomor_sertifikat' => $nomorSertifikat,
                'kode_verifikasi' => $kodeVerifikasi,
                'tanggal_terbit' => $tanggalTerbit,
                'path_file' => $path,
                'dibuat_oleh' => $dibuatOleh ?? $peserta->user_id,
            ]
        );
    }
}
