<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Pembimbing;
use App\Models\Pengaturan;
use App\Models\Peserta;
use App\Models\User;
use App\Services\DokumenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlurMagangTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('privat');
        Mail::fake();

        foreach ([
            'poin_pengurangan_izin' => '1',
            'poin_pengurangan_alpa' => '5',
            'bobot_pembimbing' => '60',
            'bobot_kehadiran' => '40',
            'nama_pic' => 'Bagus Wicaksono',
            'jabatan_pic' => 'Manager HRGA',
            'nomor_surat_terakhir' => '0',
            'nomor_sertifikat_terakhir' => '0',
        ] as $kunci => $nilai) {
            Pengaturan::set($kunci, $nilai);
        }
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /**
     * @return array{0: User, 1: Pembimbing}
     */
    private function pembimbing(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_PEMBIMBING]);

        $pembimbing = Pembimbing::create([
            'user_id' => $user->id,
            'nip' => '198705122010011003',
            'divisi' => 'IT - Pengembangan Sistem',
            'jabatan' => 'Supervisor',
        ]);

        return [$user, $pembimbing];
    }

    private function daftarPeserta(string $email = 'peserta@test.test'): array
    {
        $this->post('/register', [
            'name' => 'Rizky Test',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'no_hp' => '081234567890',
            'instansi_pendidikan' => 'SMK Negeri 3 Samarinda',
            'jurusan' => 'Teknik Komputer dan Jaringan',
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', $email)->firstOrFail();

        return [$user, $user->peserta];
    }

    public function test_alur_lengkap_pendaftaran_sampai_verifikasi_sertifikat(): void
    {
        $this->travelTo(Carbon::create(2026, 10, 12, 8, 0, 0)); // Senin

        $admin = $this->admin();
        [$pembimbingUser, $pembimbing] = $this->pembimbing();
        [$pesertaUser, $peserta] = $this->daftarPeserta();

        $this->assertSame(Peserta::STATUS_PENDAFTARAN, $peserta->status_tahap);
        $this->assertDatabaseHas('riwayat_status', ['peserta_id' => $peserta->id, 'status_baru' => 'pendaftaran']);

        // ===== Tahap 1: data + 4 berkas =====
        $mulai = now()->toDateString();
        $selesai = now()->addDays(30)->toDateString();

        $this->actingAs($pesertaUser)->put(route('pendaftaran.update'), [
            'nama_lengkap' => 'Rizky Test',
            'nik' => '6473012345678901',
            'instansi_pendidikan' => 'SMK Negeri 3 Samarinda',
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'jenjang' => 'SMK',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Pahlawan No. 1, Samarinda',
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'divisi_diajukan' => 'IT - Pengembangan Sistem',
            'surat_pengantar' => UploadedFile::fake()->create('surat-pengantar.pdf', 50, 'application/pdf'),
            'cv' => UploadedFile::fake()->create('cv.pdf', 50, 'application/pdf'),
            'kartu_identitas' => UploadedFile::fake()->image('ktp.jpg'),
            'transkrip_nilai' => UploadedFile::fake()->create('transkrip.pdf', 50, 'application/pdf'),
        ])->assertRedirect(route('pendaftaran.edit'));

        $peserta->refresh();
        $this->assertSame(Peserta::STATUS_SELEKSI, $peserta->status_tahap);
        $this->assertSame(4, $peserta->dokumenTahap1()->count());

        // Simpan ulang tanpa mengunggah berkas (regression: berkas opsional bila sudah ada).
        $this->actingAs($pesertaUser)->put(route('pendaftaran.update'), [
            'nama_lengkap' => 'Rizky Pratama Test',
            'instansi_pendidikan' => 'SMK Negeri 3 Samarinda',
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'jenjang' => 'SMK',
            'no_hp' => '081234567890',
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'divisi_diajukan' => 'IT - Pengembangan Sistem',
        ])->assertSessionHasNoErrors();

        $peserta->refresh();
        $this->assertSame('Rizky Pratama Test', $peserta->nama_lengkap);
        $this->assertSame(Peserta::STATUS_SELEKSI, $peserta->status_tahap);

        // ===== Tahap 2: seleksi administrasi =====
        $this->actingAs($admin)->post(route('admin.seleksi.store', $peserta), [
            'berkas_lengkap' => '1',
            'kompetensi_sesuai' => '1',
            'periode_sesuai' => '1',
            'keputusan' => 'diterima',
            'catatan' => 'Berkas lengkap dan sesuai.',
            'pembimbing_id' => $pembimbing->id,
            'divisi_penempatan' => 'IT - Pengembangan Sistem',
        ])->assertRedirect(route('admin.pendaftar.show', $peserta));

        $peserta->refresh();
        $this->assertSame(Peserta::STATUS_PEMENUHAN, $peserta->status_tahap);
        $this->assertSame($pembimbing->id, $peserta->pembimbing_id);

        // ===== Tahap 3: unggah 3 dokumen =====
        foreach (DokumenService::JENIS_TAHAP_3 as $jenis) {
            $this->actingAs($pesertaUser)
                ->from(route('dokumen.tahap3'))
                ->post(route('dokumen.tahap3.upload', $jenis), [
                    'file' => UploadedFile::fake()->create($jenis . '.pdf', 50, 'application/pdf'),
                ])->assertRedirect(route('dokumen.tahap3'));
        }

        $this->assertSame(3, $peserta->dokumenTahap3()->count());

        // ===== Verifikasi Tahap 3 oleh admin =====
        $this->actingAs($admin)->post(route('admin.dokumen.verifikasi-semua', $peserta), [
            'jenis' => DokumenService::JENIS_TAHAP_3,
            'status' => ['valid', 'valid', 'valid'],
            'catatan' => ['sesuai', 'sesuai', 'sesuai'],
        ])->assertRedirect();

        $peserta->refresh();
        $this->assertSame(Peserta::STATUS_AKTIF, $peserta->status_tahap);
        $this->assertNotNull($peserta->suratBalasan()->first());
        $this->assertNotNull($peserta->nilaiKehadiran);

        // ===== Absensi (hari kerja & dalam periode) =====
        $this->actingAs($pesertaUser)->post(route('absensi.masuk'))->assertRedirect();
        $this->assertDatabaseHas('absensi', [
            'peserta_id' => $peserta->id,
            'tanggal' => now()->toDateString(),
            'status' => Absensi::STATUS_HADIR,
        ]);

        // ===== Pembimbing input nilai =====
        $this->actingAs($pembimbingUser)->post(route('pembimbing.nilai', $peserta), [
            'disiplin' => 90,
            'kerja_sama' => 85,
            'inisiatif' => 88,
            'kualitas_kerja' => 92,
            'komunikasi' => 87,
            'catatan' => 'Kerja baik.',
        ])->assertRedirect(route('pembimbing.show', $peserta));

        $peserta->refresh();
        $this->assertNotNull($peserta->penilaian);
        $this->assertNotNull($peserta->nilaiAkhir);
        $this->assertNotNull($peserta->nilaiAkhir->nilai_akhir);

        // ===== Sertifikat setelah periode berakhir =====
        $this->travelTo(Carbon::create(2026, 12, 1, 9, 0, 0));

        $this->actingAs($admin)->post(route('admin.sertifikat.generate', $peserta))->assertRedirect();

        $peserta->refresh();
        $this->assertNotNull($peserta->sertifikat);
        $this->assertSame(Peserta::STATUS_SELESAI, $peserta->status_tahap);
        $sertifikat = $peserta->sertifikat;

        // Peserta mengunduh sertifikat & surat balasan dari dashboard.
        $this->actingAs($pesertaUser)->get(route('dokumen.saya.sertifikat'))->assertOk();
        $this->actingAs($pesertaUser)->get(route('dokumen.saya.surat'))->assertOk();

        // ===== Verifikasi publik QR =====
        $this->get(route('verifikasi.sertifikat', $sertifikat->kode_verifikasi))
            ->assertOk()
            ->assertSee($peserta->nama_lengkap);

        $this->get(route('verifikasi.sertifikat', 'KODE-TIDAK-ADA'))
            ->assertOk()
            ->assertSee('Sertifikat Tidak Ditemukan');
    }

    public function test_halaman_utama_setiap_role_dapat_dibuka(): void
    {
        $admin = $this->admin();
        [$pembimbingUser, $pembimbing] = $this->pembimbing();
        [$pesertaUser, $peserta] = $this->daftarPeserta('peserta2@test.test');

        // Peserta tahap pendaftaran.
        $this->actingAs($pesertaUser)->get(route('dashboard'))->assertRedirect(route('dashboard.peserta'));
        $this->actingAs($pesertaUser)->get(route('dashboard.peserta'))->assertOk();
        $this->actingAs($pesertaUser)->get(route('pendaftaran.edit'))->assertOk();

        // Admin.
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pendaftar.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pendaftar.show', $peserta))->assertOk();
        $this->actingAs($admin)->get(route('admin.absensi.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.akun-peserta.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pembimbing.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.hari-libur.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pengaturan.edit'))->assertOk();

        // Pembimbing.
        $this->actingAs($pembimbingUser)->get(route('pembimbing.dashboard'))->assertOk();

        // Peserta aktif: halaman dokumen tahap 3, absensi, dan detail pembimbing.
        $peserta->update([
            'status_tahap' => Peserta::STATUS_AKTIF,
            'pembimbing_id' => $pembimbing->id,
            'divisi_penempatan' => 'IT',
        ]);

        $this->actingAs($pesertaUser)->get(route('dokumen.tahap3'))->assertOk();
        $this->actingAs($pesertaUser)->get(route('absensi.index'))->assertOk();
        $this->actingAs($pembimbingUser)->get(route('pembimbing.show', $peserta))->assertOk();
        $this->actingAs($admin)->get(route('admin.pendaftar.show', $peserta->fresh()))->assertOk();
    }

    public function test_peserta_tidak_dapat_membuka_menu_admin_dan_absensi_sebelum_aktif(): void
    {
        [$pesertaUser, $peserta] = $this->daftarPeserta('peserta3@test.test');

        // Role middleware memblokir admin.
        $this->actingAs($pesertaUser)->get(route('admin.dashboard'))->assertForbidden();

        // Absensi diblokir sebelum status aktif.
        $this->assertSame(Peserta::STATUS_PENDAFTARAN, $peserta->status_tahap);
        $this->actingAs($pesertaUser)->get(route('absensi.index'))->assertForbidden();
        $this->actingAs($pesertaUser)->post(route('absensi.masuk'))->assertForbidden();
    }

    public function test_izin_sakit_menolak_hari_libur_dan_menimpa_absen(): void
    {
        $this->travelTo(Carbon::create(2026, 10, 12, 8, 0, 0)); // Senin

        [$pesertaUser, $peserta] = $this->daftarPeserta('peserta4@test.test');
        $peserta->update([
            'status_tahap' => Peserta::STATUS_AKTIF,
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ]);

        // Tolak pengajuan pada akhir pekan.
        $this->actingAs($pesertaUser)->post(route('absensi.ajukan'), [
            'tanggal' => now()->next(Carbon::SATURDAY)->toDateString(),
            'status' => 'izin',
            'keterangan' => 'Keperluan keluarga',
        ])->assertSessionHasErrors('tanggal');

        // Absen masuk lalu coba timpa dengan izin di hari yang sama.
        $this->actingAs($pesertaUser)->post(route('absensi.masuk'))->assertRedirect();

        $this->actingAs($pesertaUser)->post(route('absensi.ajukan'), [
            'tanggal' => now()->toDateString(),
            'status' => 'izin',
            'keterangan' => 'Keperluan keluarga',
        ])->assertSessionHasErrors('tanggal');

        $this->assertDatabaseHas('absensi', [
            'peserta_id' => $peserta->id,
            'tanggal' => now()->toDateString(),
            'status' => Absensi::STATUS_HADIR,
        ]);
    }
}
