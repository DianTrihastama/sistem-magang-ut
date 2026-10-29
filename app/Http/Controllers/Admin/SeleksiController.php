<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\HasilSeleksiMail;
use App\Models\Peserta;
use App\Models\SeleksiAdministrasi;
use App\Services\DokumenService;
use App\Services\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SeleksiController extends Controller
{
    public function store(Request $request, Peserta $peserta): RedirectResponse
    {
        $request->validate([
            'berkas_lengkap' => ['required', 'boolean'],
            'kompetensi_sesuai' => ['required', 'boolean'],
            'periode_sesuai' => ['required', 'boolean'],
            'keputusan' => ['required', Rule::in(['diterima', 'ditolak', 'revisi'])],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'pembimbing_id' => ['required_if:keputusan,diterima', 'nullable', 'exists:pembimbing,id'],
            'divisi_penempatan' => ['required_if:keputusan,diterima', 'nullable', 'string', 'max:255'],
        ], [
            'berkas_lengkap.required' => 'Centang aspek kelengkapan berkas.',
            'kompetensi_sesuai.required' => 'Centang aspek kesesuaian kompetensi.',
            'periode_sesuai.required' => 'Centang aspek kesesuaian periode.',
            'pembimbing_id.required_if' => 'Pembimbing wajib dipilih untuk pendaftar yang diterima.',
            'divisi_penempatan.required_if' => 'Divisi penempatan wajib diisi untuk pendaftar yang diterima.',
        ]);

        $lengkap = $peserta->dokumenTahap1()->count() === count(DokumenService::JENIS_TAHAP_1);

        if ($request->keputusan === 'diterima' && ! $lengkap) {
            return back()
                ->withErrors(['keputusan' => 'Peserta belum melengkapi 4 berkas Tahap 1, sehingga belum dapat dinyatakan diterima.'])
                ->withInput();
        }

        SeleksiAdministrasi::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'berkas_lengkap' => $request->boolean('berkas_lengkap') && $lengkap,
                'kompetensi_sesuai' => $request->boolean('kompetensi_sesuai'),
                'periode_sesuai' => $request->boolean('periode_sesuai'),
                'keputusan' => $request->keputusan,
                'catatan' => $request->catatan,
                'dinilai_oleh' => $request->user()->id,
            ]
        );

        $statusService = app(StatusService::class);

        switch ($request->keputusan) {
            case 'diterima':
                $peserta->fill([
                    'pembimbing_id' => $request->pembimbing_id,
                    'divisi_penempatan' => $request->divisi_penempatan,
                ])->save();

                $statusService->ubah($peserta, Peserta::STATUS_PEMENUHAN, $request->user(), 'Diterima pada seleksi administrasi');
                break;

            case 'revisi':
                $statusService->ubah($peserta, Peserta::STATUS_PENDAFTARAN, $request->user(), 'Perlu revisi berkas: ' . ($request->catatan ?? '-'));
                break;

            default:
                $statusService->ubah($peserta, Peserta::STATUS_DITOLAK, $request->user(), 'Ditolak pada seleksi administrasi');
                break;
        }

        Mail::to($peserta->user->email)->queue(
            new HasilSeleksiMail($peserta, $request->keputusan, $request->catatan)
        );

        if ($request->keputusan === 'diterima') {
            Mail::to($peserta->user->email)->queue(
                new \App\Mail\VerifikasiDokumenMail($peserta, [], true)
            );
        }

        return redirect()
            ->route('admin.pendaftar.show', $peserta)
            ->with('success', 'Keputusan seleksi administrasi telah disimpan.');
    }
}
