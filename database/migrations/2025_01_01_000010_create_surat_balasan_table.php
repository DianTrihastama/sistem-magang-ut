<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_balasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta penerima surat');
            $table->string('nomor_surat')->unique()->comment('Nomor surat otomatis');
            $table->date('tanggal_surat')->comment('Tanggal terbit surat');
            $table->string('path_file')->comment('File PDF surat balasan');
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete()->comment('Admin pembuat surat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_balasan');
    }
};
