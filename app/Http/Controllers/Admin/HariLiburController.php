<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.hari-libur.index', [
            'daftar' => HariLibur::orderBy('tanggal')->paginate(15)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tanggal' => ['required', 'date', 'unique:hari_libur,tanggal'],
            'keterangan' => ['required', 'string', 'max:255'],
        ], [
            'tanggal.unique' => 'Tanggal tersebut sudah terdaftar sebagai hari libur.',
        ]);

        HariLibur::create($request->only(['tanggal', 'keterangan']));

        return back()->with('success', 'Hari libur ditambahkan.');
    }

    public function destroy(HariLibur $hariLibur): RedirectResponse
    {
        $hariLibur->delete();

        return back()->with('success', 'Hari libur dihapus.');
    }
}
