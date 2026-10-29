<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dokumen extends Model
{
        protected $table = 'dokumen';

public const JENIS_LABELS = [
        'surat_pengantar' => 'Surat Pengantar dari Instansi',
        'cv' => 'Curriculum Vitae (CV)',
        'kartu_identitas' => 'KTP / Kartu Pelajar',
        'transkrip_nilai' => 'Transkrip Nilai',
        'bpjs_kesehatan' => 'Kartu Kepesertaan BPJS',
        'asuransi_kecelakaan' => 'Kartu Asuransi / Perlindungan Kecelakaan Kerja',
        'surat_sehat' => 'Surat Keterangan Sehat',
    ];

    protected $fillable = [
        'peserta_id',
        'jenis',
        'tahap',
        'path_file',
        'nama_asli',
        'status_verifikasi',
        'catatan',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'tahap' => 'integer',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class);
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function labelJenis(): string
    {
        return self::JENIS_LABELS[$this->jenis] ?? $this->jenis;
    }
}
