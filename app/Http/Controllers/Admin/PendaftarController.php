<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftarController extends Controller
{
    public function index(Request $request): View
    {
        $query = Peserta::with(['user', 'pembimbing.user', 'dokumen']);

        if ($request->filled('status')) {
            $query->where('status_tahap', $request->string('status'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('instansi_pendidikan', 'like', "%{$q}%")
                    ->orWhere('jurusan', 'like', "%{$q}%");
            });
        }

        return view('admin.pendaftar.index', [
            'daftar' => $query->latest('updated_at')->paginate(12)->withQueryString(),
            'filter' => $request->only(['status', 'q']),
            'statusLabels' => Peserta::STATUS_LABELS,
        ]);
    }

    public function show(int $peserta): View
    {
        $data = Peserta::with([
            'user',
            'dokumen',
            'seleksi',
            'pembimbing.user',
            'riwayatStatus.oleh',
            'nilaiKehadiran',
            'penilaian.pembimbing.user',
            'nilaiAkhir',
            'suratBalasan',
            'sertifikat',
        ])->findOrFail($peserta);

        $pembimbingList = \App\Models\Pembimbing::with('user')->get();

        return view('admin.pendaftar.show', [
            'peserta' => $data,
            'pembimbingList' => $pembimbingList,
        ]);
    }
}
