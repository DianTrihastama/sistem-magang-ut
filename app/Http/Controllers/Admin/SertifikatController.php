<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SertifikatTerbitMail;
use App\Models\Peserta;
use App\Services\NilaiAkhirService;
use App\Services\SertifikatService;
use App\Services\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    /**
     * Generate sertifikat (hanya bila periode selesai & nilai sudah diinput).
     */
    public function generate(Request $request, Peserta $peserta): RedirectResponse
    {
        if (! $peserta->periodeSelesai()) {
            return back()->with('error', 'Periode magang peserta belum berakhir.');
        }

        if (! $peserta->penilaian || ! $peserta->nilaiAkhir?->nilai_akhir) {
            return back()->with('error', 'Nilai dari pembimbing belum diinput.');
        }

        app(NilaiAkhirService::class)->hitung($peserta->fresh(['penilaian', 'nilaiKehadiran']));

        $sertifikat = app(SertifikatService::class)->generate($peserta, $request->user()->id);

        app(StatusService::class)->ubah($peserta->fresh(), Peserta::STATUS_SELESAI, $request->user(), 'Sertifikat magang diterbitkan');

        Mail::to($peserta->user->email)->queue(new SertifikatTerbitMail($peserta->fresh(), $sertifikat));

        return back()->with('success', 'Sertifikat terbit: ' . $sertifikat->nomor_sertifikat);
    }

    public function kirim(Request $request, Peserta $peserta): RedirectResponse
    {
        abort_unless($peserta->sertifikat, 422, 'Sertifikat belum digenerate.');

        Mail::to($peserta->user->email)->queue(new SertifikatTerbitMail($peserta, $peserta->sertifikat));

        return back()->with('success', 'Email sertifikat dikirim ulang ke peserta.');
    }

    public function download(Peserta $peserta)
    {
        abort_unless($peserta->sertifikat, 404);
        abort_unless(Storage::disk('privat')->exists($peserta->sertifikat->path_file), 404);

        return Storage::disk('privat')->download(
            $peserta->sertifikat->path_file,
            'Sertifikat-' . str_replace(['/', '\\'], '-', $peserta->sertifikat->nomor_sertifikat) . '.pdf'
        );
    }
}
