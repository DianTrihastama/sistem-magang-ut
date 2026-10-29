<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Peserta;
use App\Services\NilaiKehadiranService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    /**
     * Riwayat kehadiran peserta (tanpa nilai angka).
     */
    public function index(Request $request): View
    {
        $peserta = $this->pesertaAktif($request);

        $riwayat = Absensi::where('peserta_id', $peserta->id)
            ->latest('tanggal')
            ->latest('id')
            ->paginate(15);

        $hariIni = now()->toDateString();

        return view('absensi.index', [
            'peserta' => $peserta,
            'riwayat' => $riwayat,
            'sudahAbsen' => Absensi::where('peserta_id', $peserta->id)->where('tanggal', $hariIni)->exists(),
            'bolehAbsen' => $this->bolehAbsen($peserta, $hariIni, true),
        ]);
    }

    public function masuk(Request $request): RedirectResponse
    {
        $peserta = $this->pesertaAktif($request);
        $tanggal = now()->toDateString();

        if (! $this->bolehAbsen($peserta, $tanggal)) {
            return back()->withErrors(['absen' => $this->alasanTidakBoleh($peserta, $tanggal)]);
        }

        Absensi::updateOrCreate(
            ['peserta_id' => $peserta->id, 'tanggal' => $tanggal],
            [
                'jam_masuk' => now()->format('H:i'),
                'status' => Absensi::STATUS_HADIR,
                'keterangan' => null,
                'disetujui' => null,
            ]
        );

        app(NilaiKehadiranService::class)->hitung($peserta);

        return back()->with('success', 'Absen masuk berhasil dicatat pukul ' . now()->format('H:i') . '.');
    }

    public function ajukanIzin(Request $request): RedirectResponse
    {
        $peserta = $this->pesertaAktif($request);

        $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:izin,sakit'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'lampiran.mimes' => 'Lampiran harus berformat PDF, JPG, atau PNG.',
            'lampiran.max' => 'Ukuran lampiran maksimal 2 MB.',
        ]);

        $carbon = Carbon::parse($request->tanggal);
        $tanggal = $carbon->toDateString();

        if (in_array($carbon->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true)) {
            return back()->withErrors(['tanggal' => 'Pengajuan izin/sakit hanya untuk hari kerja (Senin-Jumat).'])->withInput();
        }

        if (HariLibur::where('tanggal', $tanggal)->exists()) {
            return back()->withErrors(['tanggal' => 'Tanggal tersebut merupakan hari libur nasional/perusahaan.'])->withInput();
        }

        if ($peserta->tanggal_mulai && $carbon->lt($peserta->tanggal_mulai)) {
            return back()->withErrors(['tanggal' => 'Tanggal di luar periode magang.'])->withInput();
        }

        if ($peserta->tanggal_selesai && $carbon->gt($peserta->tanggal_selesai)) {
            return back()->withErrors(['tanggal' => 'Tanggal di luar periode magang.'])->withInput();
        }

        $existing = Absensi::where('peserta_id', $peserta->id)->where('tanggal', $tanggal)->first();

        if ($existing && $existing->status === Absensi::STATUS_HADIR) {
            return back()->withErrors(['tanggal' => 'Anda sudah absen masuk pada tanggal tersebut, tidak dapat mengajukan izin/sakit.'])->withInput();
        }

        $lampiran = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('absensi/' . $peserta->id, 'privat')
            : $existing?->lampiran;

        Absensi::updateOrCreate(
            ['peserta_id' => $peserta->id, 'tanggal' => $tanggal],
            [
                'status' => $request->status,
                'jam_masuk' => null,
                'keterangan' => $request->keterangan,
                'lampiran' => $lampiran,
                'disetujui' => null,
                'disetujui_oleh' => null,
            ]
        );

        app(NilaiKehadiranService::class)->hitung($peserta);

        return redirect()->route('absensi.index')->with('success', 'Pengajuan ' . $request->status . ' dikirim, menunggu persetujuan admin.');
    }

    private function pesertaAktif(Request $request): Peserta
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        abort_unless(
            in_array($peserta->status_tahap, [Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI], true),
            403,
            'Menu absensi hanya tersedia setelah status Aktif Magang.'
        );

        return $peserta;
    }

    private function bolehAbsen(Peserta $peserta, string $tanggal, bool $hindariAbsenHariIni = false): bool
    {
        return $this->alasanTidakBoleh($peserta, $tanggal, $hindariAbsenHariIni) === null;
    }

    private function alasanTidakBoleh(Peserta $peserta, string $tanggal, bool $hindariAbsenHariIni = false): ?string
    {
        $carbon = Carbon::parse($tanggal);

        if ($carbon->dayOfWeek === Carbon::SATURDAY || $carbon->dayOfWeek === Carbon::SUNDAY) {
            return 'Hari ini hari libur (akhir pekan). Absensi hanya Senin-Jumat.';
        }

        if (HariLibur::where('tanggal', $tanggal)->exists()) {
            return 'Hari ini merupakan hari libur nasional/perusahaan.';
        }

        if ($peserta->tanggal_mulai && $carbon->lt($peserta->tanggal_mulai)) {
            return 'Absensi belum dapat dilakukan karena masih di luar periode magang.';
        }

        if ($peserta->tanggal_selesai && $carbon->gt($peserta->tanggal_selesai)) {
            return 'Absensi tidak dapat dilakukan karena periode magang sudah berakhir.';
        }

        if (! $hindariAbsenHariIni && Absensi::where('peserta_id', $peserta->id)->where('tanggal', $tanggal)->exists()) {
            return 'Anda sudah melakukan absensi hari ini.';
        }

        return null;
    }
}
