<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratBalasan extends Model
{
    protected $table = 'surat_balasan';

    protected $fillable = [
        'peserta_id',
        'nomor_surat',
        'tanggal_surat',
        'path_file',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal_surat' => 'date'];
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
