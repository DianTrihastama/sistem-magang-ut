<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $perTahap = Peserta::selectRaw('status_tahap, COUNT(*) as jumlah')
            ->groupBy('status_tahap')
            ->pluck('jumlah', 'status_tahap');

        $total = Peserta::count();

        $grafik = [
            ['label' => 'Pendaftaran', 'jumlah' => (int) $perTahap->get('pendaftaran', 0)],
            ['label' => 'Seleksi Administrasi', 'jumlah' => (int) $perTahap->get('seleksi_administrasi', 0)],
            ['label' => 'Pemenuhan Dokumen', 'jumlah' => (int) $perTahap->get('pemenuhan_dokumen', 0)],
            ['label' => 'Aktif Magang', 'jumlah' => (int) $perTahap->get('aktif', 0)],
            ['label' => 'Selesai', 'jumlah' => (int) $perTahap->get('selesai', 0)],
            ['label' => 'Ditolak', 'jumlah' => (int) $perTahap->get('ditolak', 0)],
        ];

        $maks = max(1, max(array_column($grafik, 'jumlah')));

        return view('admin.dashboard', [
            'stats' => [
                'total' => $total,
                'seleksi' => (int) $perTahap->get('seleksi_administrasi', 0),
                'pemenuhan' => (int) $perTahap->get('pemenuhan_dokumen', 0),
                'aktif' => (int) $perTahap->get('aktif', 0),
                'selesai' => (int) $perTahap->get('selesai', 0),
                'ditolak' => (int) $perTahap->get('ditolak', 0),
                'menungguIzin' => Absensi::whereNull('disetujui')
                    ->whereIn('status', ['izin', 'sakit'])
                    ->count(),
            ],
            'grafik' => $grafik,
            'maks' => $maks,
            'terbaru' => Peserta::with('user')->latest('updated_at')->limit(8)->get(),
        ]);
    }
}
