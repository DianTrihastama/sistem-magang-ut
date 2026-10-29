<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Pemilik dokumen');
            $table->enum('jenis', [
                'surat_pengantar',
                'cv',
                'kartu_identitas',
                'transkrip_nilai',
                'bpjs_kesehatan',
                'asuransi_kecelakaan',
                'surat_sehat',
            ])->comment('Jenis dokumen');
            $table->unsignedTinyInteger('tahap')->comment('Tahap pengunggahan dokumen (1 atau 3)');
            $table->string('path_file')->comment('Lokasi file privat (storage/app/private)');
            $table->string('nama_asli')->comment('Nama file asli saat diunggah');
            $table->enum('status_verifikasi', ['menunggu', 'valid', 'tidak_valid'])->default('menunggu')->comment('Hasil verifikasi admin');
            $table->text('catatan')->nullable()->comment('Catatan verifikasi admin');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete()->comment('Admin yang memverifikasi');
            $table->timestamp('diverifikasi_pada')->nullable()->comment('Waktu verifikasi');
            $table->timestamps();

            $table->unique(['peserta_id', 'jenis']);
            $table->index('tahap');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
