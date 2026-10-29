<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusAktifMail;
use App\Models\Peserta;
use App\Services\SuratBalasanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SuratBalasanController extends Controller
{
    public function generate(Request $request, Peserta $peserta): RedirectResponse
    {
        abort_unless(in_array($peserta->status_tahap, [Peserta::STATUS_PEMENUHAN, Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI], true), 422, 'Surat balasan hanya untuk pendaftar yang diterima.');

        $surat = app(SuratBalasanService::class)->generate($peserta, $request->user()->id);

        return back()->with('success', 'Surat balasan berhasil digenerate: ' . $surat->nomor_surat);
    }

    public function kirim(Request $request, Peserta $peserta): RedirectResponse
    {
        abort_unless($peserta->suratBalasan, 422, 'Surat balasan belum digenerate.');

        Mail::to($peserta->user->email)->queue(
            new StatusAktifMail($peserta, $peserta->suratBalasan->path_file)
        );

        return back()->with('success', 'Surat balasan dikirim ulang ke email peserta.');
    }

    public function download(Peserta $peserta)
    {
        abort_unless($peserta->suratBalasan, 404);
        abort_unless(Storage::disk('privat')->exists($peserta->suratBalasan->path_file), 404);

        return Storage::disk('privat')->download(
            $peserta->suratBalasan->path_file,
            'Surat-Balasan-' . str_replace(['/', '\\'], '-', $peserta->suratBalasan->nomor_surat) . '.pdf'
        );
    }
}
