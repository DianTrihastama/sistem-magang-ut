<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\Peserta;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DokumenService
{
    private const DISK = 'privat';

    public const JENIS_TAHAP_1 = [
        'surat_pengantar',
        'cv',
        'kartu_identitas',
        'transkrip_nilai',
    ];

    public const JENIS_TAHAP_3 = [
        'bpjs_kesehatan',
        'asuransi_kecelakaan',
        'surat_sehat',
    ];

    /**
     * Simpan / perbarui berkas peserta di storage privat (nama file di-hash).
     */
    public function simpan(Peserta $peserta, string $jenis, int $tahap, UploadedFile $file, ?int $userId = null): Dokumen
    {
        if (! in_array($jenis, array_merge(self::JENIS_TAHAP_1, self::JENIS_TAHAP_3), true)) {
            throw ValidationException::withMessages(['file' => 'Jenis dokumen tidak dikenal.']);
        }

        $this->validasiFile($file);

        $folder = 'dokumen/tahap-' . $tahap;
        $namaFile = Str::uuid()->toString() . '.' . strtolower($file->extension());
        $path = $file->storeAs($folder, $namaFile, self::DISK);

        if (! $path) {
            throw ValidationException::withMessages(['file' => 'Gagal menyimpan berkas.']);
        }

        $dokumen = Dokumen::updateOrCreate(
            ['peserta_id' => $peserta->id, 'jenis' => $jenis],
            [
                'tahap' => $tahap,
                'path_file' => $path,
                'nama_asli' => $file->getClientOriginalName(),
                'status_verifikasi' => 'menunggu',
                'catatan' => null,
                'diverifikasi_oleh' => null,
                'diverifikasi_pada' => null,
            ]
        );

        return $dokumen;
    }

    public function validasiFile(UploadedFile $file): void
    {
        $mimes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (! $file->isValid()) {
            throw ValidationException::withMessages(['file' => 'Upload gagal, coba lagi.']);
        }

        if (! in_array(strtolower($file->getClientOriginalExtension()), $mimes, true)) {
            throw ValidationException::withMessages(['file' => 'Berkas harus berformat PDF, JPG, atau PNG.']);
        }

        if (! in_array($file->getMimeType(), ['application/pdf', 'image/jpeg', 'image/png'], true)) {
            throw ValidationException::withMessages(['file' => 'Isi berkas tidak sesuai dengan format yang diizinkan.']);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'Ukuran berkas maksimal 2 MB.']);
        }
    }

    public function hapus(?Dokumen $dokumen): void
    {
        if ($dokumen && Storage::disk(self::DISK)->exists($dokumen->path_file)) {
            Storage::disk(self::DISK)->delete($dokumen->path_file);
        }

        $dokumen?->delete();
    }
}
