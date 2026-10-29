<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiKehadiran extends Model
{
        protected $table = 'nilai_kehadiran';

protected $fillable = [
        'peserta_id',
        'total_hari_kerja',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpa',
        'nilai',
        'dihitung_pada',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'double',
            'dihitung_pada' => 'datetime',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }
}
