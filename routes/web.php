<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\AbsensiExportController;
use App\Http\Controllers\Admin\AkunPesertaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HariLiburController;
use App\Http\Controllers\Admin\PendaftarController;
use App\Http\Controllers\Admin\PembimbingController as AdminPembimbingController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\SertifikatController as AdminSertifikatController;
use App\Http\Controllers\Admin\SeleksiController;
use App\Http\Controllers\Admin\SuratBalasanController as AdminSuratBalasanController;
use App\Http\Controllers\Admin\VerifikasiDokumenController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenTahap3Controller;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Pembimbing\DashboardController as PembimbingDashboardController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerifikasiSertifikatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Halaman verifikasi sertifikat publik (tanpa login)
Route::get('/verifikasi/{kode}', [VerifikasiSertifikatController::class, 'show'])
    ->name('verifikasi.sertifikat');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/peserta', [\App\Http\Controllers\PesertaDashboardController::class, 'index'])->name('dashboard.peserta');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File privat (pemilik / admin / pembimbing terkait)
    Route::get('/files/dokumen/{dokumen}', [FileController::class, 'dokumen'])->name('files.dokumen');
    Route::get('/files/surat-balasan/{suratBalasan}', [FileController::class, 'suratBalasan'])->name('files.surat');
    Route::get('/files/penilaian/{penilaian}', [FileController::class, 'penilaian'])->name('files.penilaian');
    Route::get('/files/lampiran-absensi/{absensi}', [FileController::class, 'lampiranAbsensi'])->name('files.lampiran');

    // Peserta magang
    Route::middleware('role:user')->group(function () {
        Route::get('/pendaftaran', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
        Route::put('/pendaftaran', [PendaftaranController::class, 'update'])->name('pendaftaran.update');
        Route::post('/pendaftaran/dokumen/{jenis}', [PendaftaranController::class, 'uploadDokumen'])->name('pendaftaran.dokumen');

        Route::get('/dokumen-tahap-3', [DokumenTahap3Controller::class, 'index'])->name('dokumen.tahap3');
        Route::post('/dokumen-tahap-3/{jenis}', [DokumenTahap3Controller::class, 'upload'])->name('dokumen.tahap3.upload');

        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk'])->name('absensi.masuk');
        Route::post('/absensi/ajukan', [AbsensiController::class, 'ajukanIzin'])->name('absensi.ajukan');

        Route::get('/dokumen-saya/surat-balasan', [FileController::class, 'suratSaya'])->name('dokumen.saya.surat');
        Route::get('/dokumen-saya/sertifikat', [FileController::class, 'sertifikatSaya'])->name('dokumen.saya.sertifikat');
    });

    // Pembimbing
    Route::middleware('role:pembimbing')->prefix('pembimbing')->name('pembimbing.')->group(function () {
        Route::get('/', [PembimbingDashboardController::class, 'index'])->name('dashboard');
        Route::get('/peserta/{peserta}', [PembimbingDashboardController::class, 'show'])->name('show');
        Route::post('/peserta/{peserta}/nilai', [PembimbingDashboardController::class, 'simpanNilai'])->name('nilai');
    });

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/pendaftar', [PendaftarController::class, 'index'])->name('pendaftar.index');
        Route::get('/pendaftar/{peserta}', [PendaftarController::class, 'show'])->name('pendaftar.show');
        Route::post('/pendaftar/{peserta}/seleksi', [SeleksiController::class, 'store'])->name('seleksi.store');
        Route::post('/pendaftar/{peserta}/dokumen/{dokumen}', [VerifikasiDokumenController::class, 'update'])->name('dokumen.verifikasi');
        Route::post('/pendaftar/{peserta}/verifikasi-tahap-3', [VerifikasiDokumenController::class, 'verifyAll'])->name('dokumen.verifikasi-semua');
        Route::post('/pendaftar/{peserta}/penugasan', [AdminPembimbingController::class, 'tugaskan'])->name('peserta.penugasan');
        Route::post('/pendaftar/{peserta}/surat-balasan', [AdminSuratBalasanController::class, 'generate'])->name('surat.generate');
        Route::post('/pendaftar/{peserta}/surat-balasan/kirim', [AdminSuratBalasanController::class, 'kirim'])->name('surat.kirim');
        Route::get('/pendaftar/{peserta}/surat-balasan/download', [AdminSuratBalasanController::class, 'download'])->name('surat.download');
        Route::post('/pendaftar/{peserta}/sertifikat', [AdminSertifikatController::class, 'generate'])->name('sertifikat.generate');
        Route::post('/pendaftar/{peserta}/sertifikat/kirim', [AdminSertifikatController::class, 'kirim'])->name('sertifikat.kirim');
        Route::get('/pendaftar/{peserta}/sertifikat/download', [AdminSertifikatController::class, 'download'])->name('sertifikat.download');

        Route::get('/akun-peserta', [AkunPesertaController::class, 'index'])->name('akun-peserta.index');
        Route::get('/akun-peserta/create', [AkunPesertaController::class, 'create'])->name('akun-peserta.create');
        Route::post('/akun-peserta', [AkunPesertaController::class, 'store'])->name('akun-peserta.store');
        Route::get('/akun-peserta/{peserta}/edit', [AkunPesertaController::class, 'edit'])->name('akun-peserta.edit');
        Route::put('/akun-peserta/{peserta}', [AkunPesertaController::class, 'update'])->name('akun-peserta.update');
        Route::delete('/akun-peserta/{peserta}', [AkunPesertaController::class, 'destroy'])->name('akun-peserta.destroy');

        Route::get('/pembimbing', [AdminPembimbingController::class, 'index'])->name('pembimbing.index');
        Route::get('/pembimbing/create', [AdminPembimbingController::class, 'create'])->name('pembimbing.create');
        Route::post('/pembimbing', [AdminPembimbingController::class, 'store'])->name('pembimbing.store');
        Route::get('/pembimbing/{pembimbing}/edit', [AdminPembimbingController::class, 'edit'])->name('pembimbing.edit');
        Route::put('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'update'])->name('pembimbing.update');
        Route::delete('/pembimbing/{pembimbing}', [AdminPembimbingController::class, 'destroy'])->name('pembimbing.destroy');

        Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi/{absensi}/setujui', [AdminAbsensiController::class, 'setujui'])->name('absensi.setujui');
        Route::put('/absensi/{absensi}', [AdminAbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('/absensi/{absensi}', [AdminAbsensiController::class, 'destroy'])->name('absensi.destroy');
        Route::get('/absensi/export/excel', [AbsensiExportController::class, 'excel'])->name('absensi.export.excel');
        Route::get('/absensi/export/pdf', [AbsensiExportController::class, 'pdf'])->name('absensi.export.pdf');

        Route::get('/hari-libur', [HariLiburController::class, 'index'])->name('hari-libur.index');
        Route::post('/hari-libur', [HariLiburController::class, 'store'])->name('hari-libur.store');
        Route::delete('/hari-libur/{hariLibur}', [HariLiburController::class, 'destroy'])->name('hari-libur.destroy');

        Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });
});

require __DIR__.'/auth.php';
