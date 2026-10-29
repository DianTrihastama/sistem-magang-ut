<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Dokumen;
use App\Models\NilaiAkhir;
use App\Models\Peserta;
use App\Models\PenilaianPembimbing;
use App\Models\SuratBalasan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyajikan file privat hanya untuk pemilik, admin, dan pembimbing terkait.
 */
class FileController extends Controller
{
    public function dokumen(Request $request, Dokumen $dokumen): Response
    {
        $this->authorizePeserta($request, $dokumen->peserta);

        return $this->stream($dokumen->path_file, $dokumen->nama_asli);
    }

    public function suratBalasan(Request $request, SuratBalasan $suratBalasan): Response
    {
        $this->authorizePeserta($request, $suratBalasan->peserta);

        return $this->stream($suratBalasan->path_file, 'Surat-Balasan-' . $this->namaAman($suratBalasan->nomor_surat) . '.pdf');
    }

    public function penilaian(Request $request, PenilaianPembimbing $penilaian): Response
    {
        abort_unless($penilaian->file_penilaian, 404);
        $this->authorizePeserta($request, $penilaian->peserta);

        return $this->stream($penilaian->file_penilaian, 'Penilaian-' . $penilaian->peserta->nama_lengkap . '.pdf');
    }

    /**
     * Unduh surat balasan milik peserta yang sedang login.
     */
    public function suratSaya(Request $request)
    {
        $peserta = $request->user()->peserta;

        abort_unless($peserta?->suratBalasan, 404);

        return $this->download($peserta->suratBalasan->path_file, 'Surat-Balasan-Magang.pdf');
    }

    /**
     * Unduh sertifikat milik peserta yang sedang login.
     */
    public function sertifikatSaya(Request $request)
    {
        $peserta = $request->user()->peserta;

        abort_unless($peserta?->sertifikat, 404);

        return $this->download($peserta->sertifikat->path_file, 'Sertifikat-Magang-' . $this->namaAman($peserta->sertifikat->nomor_sertifikat) . '.pdf');
    }

    public function lampiranAbsensi(Request $request, Absensi $absensi): Response
    {
        abort_unless($absensi->lampiran, 404);
        $this->authorizePeserta($request, $absensi->peserta);

        return $this->stream($absensi->lampiran, 'Lampiran-' . $absensi->tanggal->format('Y-m-d'));
    }

    private function authorizePeserta(Request $request, Peserta $peserta): void
    {
        $user = $request->user();

        abort_unless($user, 403);

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isPeserta() && $user->id === $peserta->user_id) {
            return;
        }

        if ($user->isPembimbing() && $peserta->pembimbing_id === $user->pembimbing?->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke berkas ini.');
    }

    private function namaAman(string $nama): string
    {
        return str_replace(['/', '\\'], '-', $nama);
    }

    private function stream(string $path, string $nama): Response
    {
        abort_unless(\Illuminate\Support\Facades\Storage::disk('privat')->exists($path), 404);

        $mime = \Illuminate\Support\Facades\Storage::disk('privat')->mimeType($path);

        return response(
            \Illuminate\Support\Facades\Storage::disk('privat')->get($path),
            200,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($nama) . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    private function download(string $path, string $nama): Response
    {
        abort_unless(\Illuminate\Support\Facades\Storage::disk('privat')->exists($path), 404);

        return \Illuminate\Support\Facades\Storage::disk('privat')->download($path, $nama);
    }
}
