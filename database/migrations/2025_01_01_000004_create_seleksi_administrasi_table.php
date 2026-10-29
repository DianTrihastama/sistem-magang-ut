<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seleksi_administrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta yang dinilai');
            $table->boolean('berkas_lengkap')->default(false)->comment('Kelengkapan berkas administrasi');
            $table->boolean('kompetensi_sesuai')->default(false)->comment('Kesesuaian kompetensi dengan kegiatan perusahaan');
            $table->boolean('periode_sesuai')->default(false)->comment('Kesesuaian periode dengan jadwal perusahaan');
            $table->enum('keputusan', ['diterima', 'ditolak', 'revisi'])->comment('Keputusan seleksi administrasi');
            $table->text('catatan')->nullable()->comment('Catatan hasil seleksi');
            $table->foreignId('dinilai_oleh')->constrained('users')->restrictOnDelete()->comment('Admin PIC prakerin');
            $table->timestamps();

            $table->unique('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seleksi_administrasi');
    }
};
