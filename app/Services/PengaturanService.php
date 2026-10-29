<?php

namespace App\Services;

use App\Models\Pengaturan;

class PengaturanService
{
    public static function angka(string $kunci, float $default = 0): float
    {
        $nilai = Pengaturan::get($kunci);

        return $nilai === null || $nilai === '' ? $default : (float) $nilai;
    }

    public static function teks(string $kunci, string $default = ''): string
    {
        $nilai = Pengaturan::get($kunci);

        return $nilai === null || $nilai === '' ? $default : $nilai;
    }

    public static function daftar(): array
    {
        return Pengaturan::query()->orderBy('kunci')->get();
    }
}
