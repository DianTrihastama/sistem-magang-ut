<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';

    protected $fillable = [
        'peserta_id',
        'status_lama',
        'status_baru',
        'oleh',
        'catatan',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function oleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'oleh');
    }
}
