<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Akun login peserta');
            $table->string('nama_lengkap')->comment('Sumber nama untuk surat balasan & sertifikat');
            $table->string('nik', 20)->nullable()->comment('Nomor induk kependudukan');
            $table->string('instansi_pendidikan')->comment('Asal sekolah / perguruan tinggi');
            $table->string('jurusan')->comment('Jurusan / bidang keahlian');
            $table->enum('jenjang', ['SMK', 'D3', 'S1', 'lainnya'])->default('SMK')->comment('Jenjang pendidikan');
            $table->string('no_hp', 20)->nullable()->comment('Nomor handphone peserta');
            $table->text('alamat')->nullable()->comment('Alamat domisili peserta');
            $table->date('tanggal_mulai')->nullable()->comment('Periode magang diajukan');
            $table->date('tanggal_selesai')->nullable()->comment('Periode magang diajukan');
            $table->string('divisi_diajukan')->nullable()->comment('Divisi / bidang yang diajukan peserta');
            $table->foreignId('pembimbing_id')->nullable()->constrained('pembimbing')->nullOnDelete()->comment('Pembimbing yang ditugaskan');
            $table->string('divisi_penempatan')->nullable()->comment('Divisi penempatan setelah diterima');
            $table->enum('status_tahap', [
                'pendaftaran',
                'seleksi_administrasi',
                'pemenuhan_dokumen',
                'aktif',
                'selesai',
                'ditolak',
            ])->default('pendaftaran')->comment('Tahap proses peserta');
            $table->timestamps();

            $table->index('status_tahap');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
