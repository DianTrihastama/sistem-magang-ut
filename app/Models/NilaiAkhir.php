<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiAkhir extends Model
{
    protected $table = 'nilai_akhir';

    protected $fillable = [
        'peserta_id',
        'nilai_pembimbing',
        'nilai_kehadiran',
        'nilai_akhir',
        'predikat',
    ];

    protected function casts(): array
    {
        return [
            'nilai_pembimbing' => 'double',
            'nilai_kehadiran' => 'double',
            'nilai_akhir' => 'double',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }
}
