<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Akun login pembimbing');
            $table->string('nip', 30)->nullable()->comment('Nomor induk pegawai');
            $table->string('divisi')->nullable()->comment('Divisi pembimbing');
            $table->string('jabatan')->nullable()->comment('Jabatan pembimbing');
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing');
    }
};
