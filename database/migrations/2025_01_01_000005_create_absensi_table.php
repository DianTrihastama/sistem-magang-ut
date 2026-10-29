<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta peserta absensi');
            $table->date('tanggal')->comment('Tanggal absensi');
            $table->time('jam_masuk')->nullable()->comment('Jam masuk aktual');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir')->comment('Status kehadiran');
            $table->text('keterangan')->nullable()->comment('Keterangan izin / sakit / koreksi admin');
            $table->string('lampiran')->nullable()->comment('Lampiran opsional (surat izin/sakit)');
            $table->boolean('disetujui')->nullable()->comment('Persetujuan admin: null=menunggu, 1=setuju, 0=tolak');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete()->comment('Admin penyetuju');
            $table->timestamps();

            $table->unique(['peserta_id', 'tanggal']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
