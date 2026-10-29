<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peserta extends Model
{
        protected $table = 'peserta';

public const STATUS_PENDAFTARAN = 'pendaftaran';
    public const STATUS_SELEKSI = 'seleksi_administrasi';
    public const STATUS_PEMENUHAN = 'pemenuhan_dokumen';
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_LABELS = [
        self::STATUS_PENDAFTARAN => 'Pendaftaran',
        self::STATUS_SELEKSI => 'Seleksi Administrasi',
        self::STATUS_PEMENUHAN => 'Pemenuhan Dokumen',
        self::STATUS_AKTIF => 'Aktif Magang',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'instansi_pendidikan',
        'jurusan',
        'jenjang',
        'no_hp',
        'alamat',
        'tanggal_mulai',
        'tanggal_selesai',
        'divisi_diajukan',
        'pembimbing_id',
        'divisi_penempatan',
        'status_tahap',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(Pembimbing::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class);
    }

    public function dokumenTahap1(): HasMany
    {
        return $this->hasMany(Dokumen::class)->where('tahap', 1);
    }

    public function dokumenTahap3(): HasMany
    {
        return $this->hasMany(Dokumen::class)->where('tahap', 3);
    }

    public function seleksi(): HasOne
    {
        return $this->hasOne(SeleksiAdministrasi::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function nilaiKehadiran(): HasOne
    {
        return $this->hasOne(NilaiKehadiran::class);
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(PenilaianPembimbing::class);
    }

    public function nilaiAkhir(): HasOne
    {
        return $this->hasOne(NilaiAkhir::class);
    }

    public function suratBalasan(): HasOne
    {
        return $this->hasOne(SuratBalasan::class);
    }

    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class);
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->latest('id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status_tahap] ?? $this->status_tahap;
    }

    public function tahapAktif(): int
    {
        return match ($this->status_tahap) {
            self::STATUS_PENDAFTARAN, self::STATUS_SELEKSI => 1,
            self::STATUS_PEMENUHAN => 3,
            self::STATUS_AKTIF, self::STATUS_SELESAI => 4,
            default => 2,
        };
    }

    public function periodeSelesai(): bool
    {
        return $this->tanggal_selesai && $this->tanggal_selesai->isPast();
    }
}
