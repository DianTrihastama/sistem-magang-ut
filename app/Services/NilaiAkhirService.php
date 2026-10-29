<?php

namespace App\Services;

use App\Models\NilaiAkhir;
use App\Models\Peserta;

class NilaiAkhirService
{
    /**
     * Gabungkan nilai pembimbing dan nilai kehadiran sesuai bobot pengaturan.
     */
    public function hitung(Peserta $peserta): ?NilaiAkhir
    {
        $penilaian = $peserta->penilaian;
        $kehadiran = $peserta->nilaiKehadiran;

        if (! $penilaian) {
            return $peserta->nilaiAkhir;
        }

        $bobotPembimbing = PengaturanService::angka('bobot_pembimbing', 80);
        $bobotKehadiran = PengaturanService::angka('bobot_kehadiran', 20);

        $nilaiKehadiran = $kehadiran->nilai ?? 100;
        $nilaiPembimbing = $penilaian->rata_rata;

        $nilaiAkhir = round(($nilaiPembimbing * $bobotPembimbing + $nilaiKehadiran * $bobotKehadiran) / 100, 2);

        return NilaiAkhir::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'nilai_pembimbing' => $nilaiPembimbing,
                'nilai_kehadiran' => $nilaiKehadiran,
                'nilai_akhir' => $nilaiAkhir,
                'predikat' => $this->predikat($nilaiAkhir),
            ]
        );
    }

    public function predikat(float $nilai): string
    {
        return match (true) {
            $nilai >= 90 => 'Sangat Baik',
            $nilai >= 80 => 'Baik',
            $nilai >= 70 => 'Cukup',
            default => 'Kurang',
        };
    }
}
