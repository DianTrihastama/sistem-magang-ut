<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'peserta_id',
        'nomor_sertifikat',
        'kode_verifikasi',
        'tanggal_terbit',
        'path_file',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal_terbit' => 'date'];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
