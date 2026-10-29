<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NomorSuratService
{
    private const BULAN_ROMAWI = [
        1 => 'JANUARI',
        2 => 'FEBRUARI',
        3 => 'MARET',
        4 => 'APRIL',
        5 => 'MEI',
        6 => 'JUNI',
        7 => 'JULI',
        8 => 'AGUSTUS',
        9 => 'SEPTEMBER',
        10 => 'OKTOBER',
        11 => 'NOVEMBER',
        12 => 'DESEMBER',
    ];

    /**
     * Nomor surat balasan: XXX/UT-SMD/HRGA/BULAN-ROMAWI/TAHUN
     */
    public function nomorSuratBalasan(): string
    {
        $baru = $this->nomorBerikutnya('nomor_surat_terakhir', 'Nomor urut surat balasan magang terakhir');

        return sprintf('%03d/UT-SMD/HRGA/%s/%s', $baru, $this->bulanRomawi(now()->month), now()->year);
    }

    /**
     * Nomor sertifikat unik: SERT-XXX/UT-SMD/HRGA/BULAN-ROMAWI/TAHUN
     */
    public function nomorSertifikat(): string
    {
        $baru = $this->nomorBerikutnya('nomor_sertifikat_terakhir', 'Nomor urut sertifikat magang terakhir');

        return sprintf('SERT-%03d/UT-SMD/HRGA/%s/%s', $baru, $this->bulanRomawi(now()->month), now()->year);
    }

    /**
     * Ambil nomor urut berikutnya secara atomik (lock baris) agar tidak duplikat saat konkuren.
     */
    private function nomorBerikutnya(string $kunci, string $keterangan): int
    {
        $baru = 0;

        DB::transaction(function () use ($kunci, $keterangan, &$baru) {
            $row = Pengaturan::query()->where('kunci', $kunci)->lockForUpdate()->first();

            if (! $row) {
                $row = Pengaturan::query()->create(['kunci' => $kunci, 'nilai' => '0', 'keterangan' => $keterangan]);
            }

            $baru = ((int) $row->nilai) + 1;
            $row->nilai = (string) $baru;
            $row->save();

            Cache::forget('pengaturan.' . $kunci);
        });

        return $baru;
    }

    public function bulanRomawi(int $bulan): string
    {
        return self::BULAN_ROMAWI[$bulan] ?? strtoupper(now()->translatedFormat('F'));
    }

    public static function kodeVerifikasi(): string
    {
        return strtoupper(Str::random(16));
    }
}
