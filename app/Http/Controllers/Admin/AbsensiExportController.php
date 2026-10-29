<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Peserta;
use App\Services\NilaiKehadiranService;
use App\Exports\AbsensiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AbsensiExportController extends Controller
{
    public function excel(Request $request): BinaryFileResponse
    {
        $nama = 'absensi-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new AbsensiExport($request->integer('peserta_id') ?: null, $request->string('bulan')->toString()),
            $nama
        );
    }

    public function pdf(Request $request)
    {
        $peserta = $request->filled('peserta_id')
            ? Peserta::findOrFail($request->integer('peserta_id'))
            : null;

        $bulan = $request->string('bulan')->toString();

        $query = Absensi::with('peserta')->orderByDesc('tanggal');

        if ($peserta) {
            $query->where('peserta_id', $peserta->id);
        }

        if ($bulan) {
            $tanggal = \Carbon\Carbon::parse($bulan . '-01');
            $query->whereBetween('tanggal', [$tanggal->copy()->startOfMonth(), $tanggal->copy()->endOfMonth()]);
        }

        $absensi = $query->get();

        $pdf = Pdf::loadView('admin.absensi.export-pdf', [
            'absensi' => $absensi,
            'peserta' => $peserta,
            'bulan' => $bulan,
            'nilaiKehadiran' => $peserta ? app(NilaiKehadiranService::class)->hitung($peserta) : null,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('absensi-' . now()->format('Ymd-His') . '.pdf');
    }
}
