<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta pemilik nilai akhir');
            $table->double('nilai_pembimbing', 5, 2)->nullable()->comment('Rata-rata penilaian pembimbing');
            $table->double('nilai_kehadiran', 5, 2)->nullable()->comment('Nilai kehadiran otomatis');
            $table->double('nilai_akhir', 5, 2)->nullable()->comment('Gabungan berbobot');
            $table->string('predikat', 20)->nullable()->comment('Sangat Baik / Baik / Cukup / Kurang');
            $table->timestamps();

            $table->unique('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_akhir');
    }
};
