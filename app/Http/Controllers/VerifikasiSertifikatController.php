<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use Illuminate\View\View;

class VerifikasiSertifikatController extends Controller
{
    public function show(string $kode): View
    {
        $sertifikat = Sertifikat::with(['peserta', 'dibuatOleh'])
            ->where('kode_verifikasi', $kode)
            ->first();

        return view('verifikasi.sertifikat', [
            'sertifikat' => $sertifikat,
            'kode' => $kode,
        ]);
    }
}
