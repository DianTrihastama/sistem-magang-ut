<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Mail\NilaiDiinputMail;
use App\Models\NilaiAkhir;
use App\Models\Peserta;
use App\Services\NilaiAkhirService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $pembimbing = $request->user()->pembimbing;

        abort_unless($pembimbing, 403);

        $daftar = Peserta::with(['nilaiKehadiran', 'nilaiAkhir', 'penilaian'])
            ->where('pembimbing_id', $pembimbing->id)
            ->latest()
            ->get();

        return view('pembimbing.dashboard', [
            'pembimbing' => $pembimbing,
            'daftar' => $daftar,
        ]);
    }

    public function show(Request $request, Peserta $peserta): View
    {
        $pembimbing = $request->user()->pembimbing;

        abort_unless($pembimbing && $peserta->pembimbing_id === $pembimbing->id, 403);

        $peserta->load(['penilaian', 'nilaiKehadiran', 'nilaiAkhir', 'absensi' => fn ($q) => $q->latest('tanggal')->limit(60)]);

        return view('pembimbing.show', [
            'pembimbing' => $pembimbing,
            'peserta' => $peserta,
        ]);
    }

    public function simpanNilai(Request $request, Peserta $peserta): RedirectResponse
    {
        $pembimbing = $request->user()->pembimbing;

        abort_unless($pembimbing && $peserta->pembimbing_id === $pembimbing->id, 403);

        $request->validate([
            'disiplin' => ['required', 'integer', 'between:0,100'],
            'kerja_sama' => ['required', 'integer', 'between:0,100'],
            'inisiatif' => ['required', 'integer', 'between:0,100'],
            'kualitas_kerja' => ['required', 'integer', 'between:0,100'],
            'komunikasi' => ['required', 'integer', 'between:0,100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'file_penilaian' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
        ], [
            '*.between' => 'Nilai harus antara 0 sampai 100.',
            'file_penilaian.mimes' => 'File penilaian harus berformat PDF.',
            'file_penilaian.max' => 'Ukuran file penilaian maksimal 2 MB.',
        ]);

        $rata = round(
            ((int) $request->disiplin + (int) $request->kerja_sama + (int) $request->inisiatif + (int) $request->kualitas_kerja + (int) $request->komunikasi) / 5,
            2
        );

        $path = $peserta->penilaian?->file_penilaian;

        if ($request->hasFile('file_penilaian')) {
            $path = $request->file('file_penilaian')->store('penilaian/' . $peserta->id, 'privat');
        }

        \App\Models\PenilaianPembimbing::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'pembimbing_id' => $pembimbing->id,
                'disiplin' => $request->disiplin,
                'kerja_sama' => $request->kerja_sama,
                'inisiatif' => $request->inisiatif,
                'kualitas_kerja' => $request->kualitas_kerja,
                'komunikasi' => $request->komunikasi,
                'rata_rata' => $rata,
                'catatan' => $request->catatan,
                'file_penilaian' => $path,
            ]
        );

        $nilaiAkhir = app(NilaiAkhirService::class)->hitung($peserta->fresh(['nilaiKehadiran', 'penilaian']));

        $admins = config('mail.admin_notify') ?: [config('mail.from.address')];

        foreach ($admins as $adminEmail) {
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new NilaiDiinputMail($peserta, $nilaiAkhir ?? new NilaiAkhir([
                    'peserta_id' => $peserta->id,
                    'nilai_akhir' => $rata,
                ])));
            }
        }

        return redirect()
            ->route('pembimbing.show', $peserta)
            ->with('success', 'Nilai peserta tersimpan. Nilai akhir: ' . ($nilaiAkhir?->nilai_akhir ?? $rata));
    }
}
