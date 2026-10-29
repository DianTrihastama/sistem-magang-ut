<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta yang dinilai');
            $table->foreignId('pembimbing_id')->constrained('pembimbing')->cascadeOnDelete()->comment('Pembimbing penilai');
            $table->unsignedTinyInteger('disiplin')->comment('Nilai disiplin 0-100');
            $table->unsignedTinyInteger('kerja_sama')->comment('Nilai kerja sama 0-100');
            $table->unsignedTinyInteger('inisiatif')->comment('Nilai inisiatif 0-100');
            $table->unsignedTinyInteger('kualitas_kerja')->comment('Nilai kualitas kerja 0-100');
            $table->unsignedTinyInteger('komunikasi')->comment('Nilai komunikasi 0-100');
            $table->double('rata_rata', 5, 2)->comment('Rata-rata 5 aspek');
            $table->text('catatan')->nullable()->comment('Catatan pembimbing');
            $table->string('file_penilaian')->nullable()->comment('File penilaian PDF (opsional)');
            $table->timestamps();

            $table->unique('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_pembimbing');
    }
};
