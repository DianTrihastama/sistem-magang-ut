<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusAktifMail;
use App\Mail\VerifikasiDokumenMail;
use App\Models\Dokumen;
use App\Models\Peserta;
use App\Services\NilaiKehadiranService;
use App\Services\StatusService;
use App\Services\SuratBalasanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerifikasiDokumenController extends Controller
{
    /**
     * Verifikasi satu dokumen Tahap 3.
     */
    public function update(Request $request, Peserta $peserta, Dokumen $dokumen): RedirectResponse
    {
        abort_unless($dokumen->peserta_id === $peserta->id, 404);
        abort_unless($dokumen->tahap === 3, 404);

        $request->validate([
            'status_verifikasi' => ['required', 'in:valid,tidak_valid'],
            'catatan' => ['required_if:status_verifikasi,tidak_valid', 'nullable', 'string', 'max:1000'],
        ], [
            'catatan.required_if' => 'Catatan wajib diisi bila dokumen tidak valid.',
        ]);

        $dokumen->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan' => $request->catatan,
            'diverifikasi_oleh' => $request->user()->id,
            'diverifikasi_pada' => now(),
        ]);

        $this->evaluasiStatus($peserta, $request);

        return back()->with('success', 'Verifikasi dokumen "' . $dokumen->labelJenis() . '" disimpan.');
    }

    /**
     * Verifikasi seluruh dokumen Tahap 3 sekaligus (dengan checklist detail).
     */
    public function verifyAll(Request $request, Peserta $peserta): RedirectResponse
    {
        $request->validate([
            'jenis' => ['required', 'array'],
            'jenis.*' => ['in:bpjs_kesehatan,asuransi_kecelakaan,surat_sehat'],
            'status' => ['required', 'array'],
            'catatan' => ['nullable', 'array'],
            'checklist' => ['nullable', 'array'],
        ]);

        foreach ($request->jenis as $index => $jenis) {
            $dokumen = $peserta->dokumenTahap3()->where('jenis', $jenis)->first();

            if (! $dokumen) {
                continue;
            }

            $status = $request->status[$index] ?? 'menunggu';
            if (! in_array($status, ['valid', 'tidak_valid'], true)) {
                continue;
            }

            $catatan = $request->catatan[$index] ?? null;

            $centang = $request->checklist[$index] ?? [];
            if (is_array($centang) && $centang !== []) {
                $rincian = 'Checklist verifikasi: ' . implode('; ', $centang);
                $catatan = trim(($catatan ? $catatan . ' | ' : '') . $rincian);
            }

            $dokumen->update([
                'status_verifikasi' => $status,
                'catatan' => $catatan,
                'diverifikasi_oleh' => $request->user()->id,
                'diverifikasi_pada' => now(),
            ]);
        }

        $this->evaluasiStatus($peserta, $request);

        return back()->with('success', 'Hasil verifikasi dokumen Tahap 3 disimpan.');
    }

    private function evaluasiStatus(Peserta $peserta, Request $request): void
    {
        $dokumen = $peserta->dokumenTahap3()->get();

        Mail::to($peserta->user->email)->queue(
            new VerifikasiDokumenMail($peserta, $dokumen->all(), false)
        );

        if ($dokumen->count() === 3 && $dokumen->every(fn ($d) => $d->status_verifikasi === 'valid')) {
            $statusService = app(StatusService::class);
            $statusService->ubah($peserta, Peserta::STATUS_AKTIF, $request->user(), 'Seluruh dokumen Tahap 3 dinyatakan valid');

            app(NilaiKehadiranService::class)->hitung($peserta);

            $surat = app(SuratBalasanService::class)->generate($peserta, $request->user()->id);

            Mail::to($peserta->user->email)->queue(
                new StatusAktifMail($peserta, $surat->path_file)
            );
        }
    }
}
