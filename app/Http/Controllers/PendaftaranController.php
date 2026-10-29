<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Peserta;
use App\Services\DokumenService;
use App\Services\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    /**
     * Tahap 1 - Formulir pendaftaran + upload 4 berkas.
     */
    public function edit(Request $request): View
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_if($peserta->status_tahap === Peserta::STATUS_DITOLAK, 403, 'Pendaftaran Anda ditolak.');

        return view('pendaftaran.index', [
            'peserta' => $peserta,
            'dokumen' => $peserta->dokumenTahap1()->get()->keyBy('jenis'),
        ]);
    }

    public function update(PendaftaranRequest $request): RedirectResponse
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_if(in_array($peserta->status_tahap, [Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI, Peserta::STATUS_DITOLAK], true), 403);

        $peserta->update($request->safe()->only([
            'nama_lengkap',
            'nik',
            'instansi_pendidikan',
            'jurusan',
            'jenjang',
            'no_hp',
            'alamat',
            'tanggal_mulai',
            'tanggal_selesai',
            'divisi_diajukan',
        ]));

        $service = app(DokumenService::class);

        foreach (DokumenService::JENIS_TAHAP_1 as $jenis) {
            if ($request->hasFile($jenis)) {
                $service->simpan($peserta, $jenis, 1, $request->file($jenis), $request->user()->id);
            }
        }

        $terupload = $peserta->dokumenTahap1()->count();

        // Hanya naik ke tahap seleksi bila peserta masih ada di tahap pendaftaran,
        // agar status yang sudah lebih lanjut (pemenuhan/aktif/selesai) tidak mundur.
        if ($terupload === 4 && $peserta->status_tahap === Peserta::STATUS_PENDAFTARAN) {
            app(StatusService::class)->ubah(
                $peserta,
                Peserta::STATUS_SELEKSI,
                $request->user(),
                'Berkas Tahap 1 lengkap, menunggu seleksi administrasi'
            );
        }

        return redirect()
            ->route('pendaftaran.edit')
            ->with('success', 'Data pendaftaran tersimpan. ' . $terupload . '/4 berkas terunggah.');
    }

    public function uploadDokumen(Request $request, string $jenis): RedirectResponse
    {
        abort_unless(in_array($jenis, DokumenService::JENIS_TAHAP_1, true), 404);

        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_if(in_array($peserta->status_tahap, [Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI, Peserta::STATUS_DITOLAK], true), 403);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'file.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'file.max' => 'Ukuran berkas maksimal 2 MB.',
        ]);

        app(DokumenService::class)->simpan($peserta, $jenis, 1, $request->file('file'), $request->user()->id);

        if ($peserta->dokumenTahap1()->count() === 4 && $peserta->status_tahap === Peserta::STATUS_PENDAFTARAN) {
            app(StatusService::class)->ubah(
                $peserta,
                Peserta::STATUS_SELEKSI,
                $request->user(),
                'Berkas Tahap 1 lengkap, menunggu seleksi administrasi'
            );
        }

        return back()->with('success', 'Berkas berhasil diunggah.');
    }
}
