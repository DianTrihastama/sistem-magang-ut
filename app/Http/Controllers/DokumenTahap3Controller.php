<?php

namespace App\Http\Controllers;

use App\Mail\VerifikasiDokumenMail;
use App\Models\Dokumen;
use App\Models\Peserta;
use App\Services\DokumenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DokumenTahap3Controller extends Controller
{
    public function index(Request $request): View
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_unless(in_array($peserta->status_tahap, [Peserta::STATUS_PEMENUHAN, Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI], true), 403, 'Anda belum masuk tahap pemenuhan dokumen.');

        return view('dokumen.tahap3', [
            'peserta' => $peserta,
            'dokumen' => $peserta->dokumenTahap3()->get()->keyBy('jenis'),
        ]);
    }

    public function upload(Request $request, string $jenis): RedirectResponse
    {
        abort_unless(in_array($jenis, DokumenService::JENIS_TAHAP_3, true), 404);

        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_unless(in_array($peserta->status_tahap, [Peserta::STATUS_PEMENUHAN, Peserta::STATUS_AKTIF], true), 403);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'file.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'file.max' => 'Ukuran berkas maksimal 2 MB.',
        ]);

        $sebelumnya = $peserta->dokumenTahap3()->count();

        app(DokumenService::class)->simpan($peserta, $jenis, 3, $request->file('file'), $request->user()->id);

        $terupload = $peserta->dokumenTahap3()->count();

        if ($terupload === 3 && $sebelumnya < 3) {
            Mail::to($peserta->user->email)->queue(new VerifikasiDokumenMail($peserta, $peserta->dokumenTahap3()->get()->all(), false));
        }

        return back()->with('success', "Berkas berhasil diunggah ({$terupload}/3).");
    }
}
