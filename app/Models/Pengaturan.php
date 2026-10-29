<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai', 'keterangan'];

    public static function get(string $kunci, ?string $default = null): ?string
    {
        $item = Cache::remember('pengaturan.' . $kunci, 60, function () use ($kunci) {
            return static::query()->where('kunci', $kunci)->first();
        });

        return $item?->nilai ?? $default;
    }

    public static function set(string $kunci, ?string $nilai, ?string $keterangan = null): void
    {
        static::query()->updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai, 'keterangan' => $keterangan]
        );
        Cache::forget('pengaturan.' . $kunci);
    }

    public static function angka(string $kunci, float $default = 0): float
    {
        return (float) (static::get($kunci) ?? $default);
    }
}
