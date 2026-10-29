<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\SuratBalasan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SuratBalasanService
{
    private const DISK = 'privat';

    /**
     * Generate PDF surat balasan magang dan simpan ke storage privat.
     */
    public function generate(Peserta $peserta, ?int $dibuatOleh = null): SuratBalasan
    {
        $nomorService = app(NomorSuratService::class);

        $existing = $peserta->suratBalasan;

        $nomorSurat = $existing?->nomor_surat ?? $nomorService->nomorSuratBalasan();
        $tanggalSurat = $existing?->tanggal_surat ?? now()->toDateString();

        $data = [
            'peserta' => $peserta,
            'nomorSurat' => $nomorSurat,
            'tanggalSurat' => $tanggalSurat,
            'namaPic' => PengaturanService::teks('nama_pic', 'PIC Prakerin'),
            'jabatanPic' => PengaturanService::teks('jabatan_pic', 'HRGA'),
        ];

        $pdf = Pdf::loadView('pdf.surat-balasan', $data)
            ->setPaper('a4', 'portrait');

        $namaFile = 'surat-balasan-' . $peserta->id . '-' . time() . '.pdf';
        $path = 'surat-balasan/' . $namaFile;

        Storage::disk(self::DISK)->put($path, $pdf->output());

        if ($existing && Storage::disk(self::DISK)->exists($existing->path_file)) {
            Storage::disk(self::DISK)->delete($existing->path_file);
        }

        return SuratBalasan::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => $tanggalSurat,
                'path_file' => $path,
                'dibuat_oleh' => $dibuatOleh ?? $peserta->user_id,
            ]
        );
    }
}
