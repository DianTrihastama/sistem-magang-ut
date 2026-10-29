<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    private const KUNCI = [
        'poin_pengurangan_izin' => 'Poin pengurangan per hari izin/sakit',
        'poin_pengurangan_alpa' => 'Poin pengurangan per hari alpa',
        'bobot_pembimbing' => 'Bobot nilai pembimbing (%)',
        'bobot_kehadiran' => 'Bobot nilai kehadiran (%)',
        'nama_pic' => 'Nama PIC / penandatangan',
        'jabatan_pic' => 'Jabatan PIC / penandatangan',
        'nomor_surat_terakhir' => 'Nomor urut surat balasan terakhir',
        'nomor_sertifikat_terakhir' => 'Nomor urut sertifikat terakhir',
    ];

    public function edit(): View
    {
        $pengaturan = Pengaturan::pluck('nilai', 'kunci');

        return view('admin.pengaturan.edit', [
            'kunci' => self::KUNCI,
            'pengaturan' => $pengaturan,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'poin_pengurangan_izin' => ['required', 'numeric', 'min:0', 'max:100'],
            'poin_pengurangan_alpa' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_pembimbing' => ['required', 'numeric', 'min:0', 'max:100'],
            'bobot_kehadiran' => ['required', 'numeric', 'min:0', 'max:100'],
            'nama_pic' => ['required', 'string', 'max:255'],
            'jabatan_pic' => ['required', 'string', 'max:255'],
            'nomor_surat_terakhir' => ['required', 'integer', 'min:0'],
            'nomor_sertifikat_terakhir' => ['required', 'integer', 'min:0'],
        ]);

        if ((float) $request->bobot_pembimbing + (float) $request->bobot_kehadiran != 100) {
            return back()->withErrors(['bobot_pembimbing' => 'Total bobot nilai harus 100%.'])->withInput();
        }

        foreach (self::KUNCI as $kunci => $label) {
            Pengaturan::set($kunci, (string) $request->input($kunci), $label);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
