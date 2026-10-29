<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete()->comment('Peserta terkait');
            $table->string('status_lama', 30)->nullable()->comment('Status sebelumnya');
            $table->string('status_baru', 30)->comment('Status sesudahnya');
            $table->foreignId('oleh')->nullable()->constrained('users')->nullOnDelete()->comment('Pihak yang mengubah');
            $table->text('catatan')->nullable()->comment('Catatan perubahan');
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
