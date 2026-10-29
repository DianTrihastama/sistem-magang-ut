<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta pemilik sertifikat');
            $table->string('nomor_sertifikat')->unique()->comment('Nomor sertifikat unik');
            $table->string('kode_verifikasi', 40)->unique()->comment('Kode verifikasi QR');
            $table->date('tanggal_terbit')->comment('Tanggal terbit sertifikat');
            $table->string('path_file')->comment('File PDF sertifikat (landscape A4)');
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete()->comment('Admin pembuat sertifikat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
