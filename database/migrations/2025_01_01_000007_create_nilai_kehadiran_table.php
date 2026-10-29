<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta pemilik nilai');
            $table->integer('total_hari_kerja')->default(0)->comment('Total hari kerja dalam periode magang');
            $table->integer('jumlah_hadir')->default(0)->comment('Jumlah hari hadir');
            $table->integer('jumlah_izin')->default(0)->comment('Jumlah hari izin');
            $table->integer('jumlah_sakit')->default(0)->comment('Jumlah hari sakit');
            $table->integer('jumlah_alpa')->default(0)->comment('Jumlah hari alpa');
            $table->double('nilai', 5, 2)->default(100)->comment('Nilai kehadiran (basis 100)');
            $table->timestamp('dihitung_pada')->nullable()->comment('Waktu perhitungan terakhir');
            $table->timestamps();

            $table->unique('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_kehadiran');
    }
};
