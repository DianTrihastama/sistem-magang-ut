<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembimbing extends Model
{
    protected $table = 'pembimbing';

    protected $fillable = [
        'user_id',
        'nip',
        'divisi',
        'jabatan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(PenilaianPembimbing::class);
    }
}
