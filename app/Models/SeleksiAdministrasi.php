<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeleksiAdministrasi extends Model
{
        protected $table = 'seleksi_administrasi';

protected $fillable = [
        'peserta_id',
        'berkas_lengkap',
        'kompetensi_sesuai',
        'periode_sesuai',
        'keputusan',
        'catatan',
        'dinilai_oleh',
    ];

    protected function casts(): array
    {
        return [
            'berkas_lengkap' => 'boolean',
            'kompetensi_sesuai' => 'boolean',
            'periode_sesuai' => 'boolean',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function dinilaiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}
