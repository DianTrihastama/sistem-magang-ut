<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IzinSakitMail;
use App\Models\Absensi;
use App\Models\Peserta;
use App\Services\NilaiKehadiranService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $query = Absensi::with(['peserta', 'disetujuiOleh']);

        if ($request->filled('peserta_id')) {
            $query->where('peserta_id', $request->integer('peserta_id'));
        }

        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->string('bulan') . '-01');
            $query->whereBetween('tanggal', [$bulan->copy()->startOfMonth(), $bulan->copy()->endOfMonth()]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->boolean('pending')) {
            $query->whereNull('disetujui')->whereIn('status', ['izin', 'sakit']);
        }

        return view('admin.absensi.index', [
            'daftar' => $query->orderByDesc('tanggal')->paginate(15)->withQueryString(),
            'pesertaList' => Peserta::orderBy('nama_lengkap')->get(),
            'filter' => $request->only(['peserta_id', 'bulan', 'status', 'pending']),
            'rekap' => $this->rekap($request),
        ]);
    }

    public function setujui(Request $request, Absensi $absensi): RedirectResponse
    {
        abort_unless($absensi->status === 'izin' || $absensi->status === 'sakit', 422);

        $request->validate(['disetujui' => ['required', 'boolean']]);

        $absensi->update([
            'disetujui' => $request->boolean('disetujui'),
            'disetujui_oleh' => $request->user()->id,
        ]);

        app(NilaiKehadiranService::class)->hitung($absensi->peserta);

        Mail::to($absensi->peserta->user->email)->queue(
            new IzinSakitMail($absensi->peserta, $absensi, $request->boolean('disetujui'))
        );

        return back()->with('success', $request->boolean('disetujui')
            ? 'Pengajuan ' . $absensi->status . ' disetujui.'
            : 'Pengajuan ' . $absensi->status . ' ditolak.');
    }

    /**
     * Koreksi data absensi oleh admin.
     */
    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:hadir,izin,sakit,alpa'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'tanggal' => ['required', 'date'],
        ]);

        $butuhPersetujuan = in_array($request->status, ['izin', 'sakit'], true);

        $absensi->update([
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'jam_masuk' => $request->status === 'hadir' ? ($request->jam_masuk ?? $absensi->jam_masuk ?? now()->format('H:i')) : null,
            'keterangan' => $request->keterangan,
            'disetujui' => $butuhPersetujuan ? $absensi->disetujui : null,
            'disetujui_oleh' => $butuhPersetujuan ? ($absensi->disetujui_oleh ?? $request->user()->id) : null,
        ]);

        app(NilaiKehadiranService::class)->hitung($absensi->peserta);

        return back()->with('success', 'Data absensi dikoreksi.');
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $peserta = $absensi->peserta;
        $absensi->delete();

        app(NilaiKehadiranService::class)->hitung($peserta);

        return back()->with('success', 'Data absensi dihapus.');
    }

    private function rekap(Request $request): array
    {
        $query = Absensi::query();

        if ($request->filled('peserta_id')) {
            $query->where('peserta_id', $request->integer('peserta_id'));
        }

        if ($request->filled('bulan')) {
            $bulan = Carbon::parse($request->string('bulan') . '-01');
            $query->whereBetween('tanggal', [$bulan->copy()->startOfMonth(), $bulan->copy()->endOfMonth()]);
        }

        $rows = $query->get();

        return [
            'total' => $rows->count(),
            'hadir' => $rows->where('status', 'hadir')->count(),
            'izin' => $rows->where('status', 'izin')->count(),
            'sakit' => $rows->where('status', 'sakit')->count(),
            'alpa' => $rows->where('status', 'alpa')->count(),
            'menunggu' => $rows->whereNull('disetujui')->whereIn('status', ['izin', 'sakit'])->count(),
        ];
    }
}
