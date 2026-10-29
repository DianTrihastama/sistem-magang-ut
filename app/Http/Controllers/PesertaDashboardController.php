<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaDashboardController extends Controller
{
    /**
     * Dashboard peserta: progres 3 tahap, status, dokumen, unduhan.
     */
    public function index(Request $request): View
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->firstOrFail();

        $peserta->load([
            'dokumen',
            'seleksi',
            'nilaiKehadiran',
            'penilaian',
            'nilaiAkhir',
            'suratBalasan',
            'sertifikat',
        ]);

        $tahapSaatIni = match ($peserta->status_tahap) {
            Peserta::STATUS_PENDAFTARAN => 1,
            Peserta::STATUS_SELEKSI => 2,
            Peserta::STATUS_PEMENUHAN => 3,
            Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI => 4,
            default => 2,
        };

        $dokumenTahap1 = $peserta->dokumen->where('tahap', 1)->keyBy('jenis');
        $dokumenTahap3 = $peserta->dokumen->where('tahap', 3)->keyBy('jenis');

        return view('dashboard.peserta', [
            'peserta' => $peserta,
            'tahapSaatIni' => $tahapSaatIni,
            'dokumenTahap1' => $dokumenTahap1,
            'dokumenTahap3' => $dokumenTahap3,
            'bolehAbsensi' => in_array($peserta->status_tahap, [Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI], true),
        ]);
    }
}
