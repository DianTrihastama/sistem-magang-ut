<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianPembimbing extends Model
{
    protected $table = 'penilaian_pembimbing';

    protected $fillable = [
        'peserta_id',
        'pembimbing_id',
        'disiplin',
        'kerja_sama',
        'inisiatif',
        'kualitas_kerja',
        'komunikasi',
        'rata_rata',
        'catatan',
        'file_penilaian',
    ];

    protected function casts(): array
    {
        return ['rata_rata' => 'double'];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(Pembimbing::class);
    }
}
