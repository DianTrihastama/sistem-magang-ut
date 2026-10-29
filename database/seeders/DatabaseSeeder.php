<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Dokumen;
use App\Models\HariLibur;
use App\Models\PenilaianPembimbing;
use App\Models\Pembimbing;
use App\Models\Pengaturan;
use App\Models\Peserta;
use App\Models\SeleksiAdministrasi;
use App\Models\User;
use App\Services\NilaiKehadiranService;
use App\Services\StatusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $this->pengaturan();
        [$admin, $bimbing1] = $this->akunDasar();
        $this->hariLibur();

        $aktif = $this->pesertaAktif($admin, $bimbing1);
        $this->pesertaSelesai($admin, $bimbing1);
        $this->pesertaLain($admin);

        $this->command?->info('Seeder selesai: akun demo admin/pembimbing/peserta (password: password).');
        unset($aktif);
    }

    private function pengaturan(): void
    {
        Pengaturan::set('poin_pengurangan_izin', '1', 'Poin pengurangan per hari izin/sakit');
        Pengaturan::set('poin_pengurangan_alpa', '5', 'Poin pengurangan per hari alpa');
        Pengaturan::set('bobot_pembimbing', '60', 'Bobot nilai pembimbing (%)');
        Pengaturan::set('bobot_kehadiran', '40', 'Bobot nilai kehadiran (%)');
        Pengaturan::set('nama_pic', 'Bagus Wicaksono', 'Nama PIC / penandatangan');
        Pengaturan::set('jabatan_pic', 'Manager HRGA', 'Jabatan PIC / penandatangan');
        Pengaturan::set('nomor_surat_terakhir', '0', 'Nomor urut surat balasan terakhir');
        Pengaturan::set('nomor_sertifikat_terakhir', '0', 'Nomor urut sertifikat terakhir');
    }

    /**
     * @return array{0: User, 1: Pembimbing, 2: Pembimbing}
     */
    private function akunDasar(): array
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ut.test'],
            ['name' => 'Administrator HRGA', 'password' => Hash::make('password'), 'role' => User::ROLE_ADMIN]
        );

        $bimbing1 = $this->pembimbing('pembimbing@ut.test', 'Budi Hartono', '198705122010011003', 'IT - Pengembangan Sistem', 'Supervisor');
        $bimbing2 = $this->pembimbing('pembimbing2@ut.test', 'Sri Wahyuni', '199002252015032004', 'HRGA', 'Staff Senior');

        return [$admin, $bimbing1, $bimbing2];
    }

    private function pembimbing(string $email, string $nama, string $nip, string $divisi, string $jabatan): Pembimbing
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $nama, 'password' => Hash::make('password'), 'role' => User::ROLE_PEMBIMBING]
        );

        return Pembimbing::firstOrCreate(
            ['user_id' => $user->id],
            ['nip' => $nip, 'divisi' => $divisi, 'jabatan' => $jabatan]
        );
    }

    private function hariLibur(): void
    {
        $tahun = now()->year;

        foreach ([
            [$tahun . '-01-01', 'Tahun Baru Masehi'],
            [$tahun . '-05-01', 'Hari Buruh Internasional'],
            [$tahun . '-06-01', 'Hari Lahir Pancasila'],
            [$tahun . '-08-17', 'Hari Kemerdekaan Republik Indonesia'],
            [$tahun . '-12-25', 'Hari Natal'],
        ] as [$tanggal, $keterangan]) {
            HariLibur::firstOrCreate(['tanggal' => $tanggal], ['keterangan' => $keterangan]);
        }
    }

    /**
     * Peserta contoh dengan status Aktif Magang: dokumen lengkap, absensi berjalan, belum dinilai.
     */
    private function pesertaAktif(User $admin, Pembimbing $pembimbing): Peserta
    {
        $peserta = $this->buatPeserta(
            email: 'peserta@ut.test',
            nama: 'Rizky Pratama',
            instansi: 'SMK Negeri 3 Samarinda',
            jurusan: 'Teknik Komputer dan Jaringan',
            jenjang: 'SMK',
            mulai: now()->subDays(20)->startOfDay(),
            selesai: now()->addDays(40)->endOfDay(),
            divisiDiajukan: 'IT - Pengembangan Sistem',
            target: Peserta::STATUS_AKTIF,
            admin: $admin,
        );

        $this->dokumenTahap1($peserta);
        $this->seleksi($peserta, $admin, 'diterima', 'Berkas lengkap dan sesuai.');
        $peserta->update([
            'pembimbing_id' => $pembimbing->id,
            'divisi_penempatan' => 'IT - Pengembangan Sistem',
        ]);
        $this->dokumenTahap3($peserta, $admin, 'valid');
        $this->absensiContoh($peserta, $admin, true);

        return $peserta;
    }

    /**
     * Peserta contoh berstatus Selesai: absensi penuh, sudah dinilai pembimbing.
     */
    private function pesertaSelesai(User $admin, Pembimbing $pembimbing): Peserta
    {
        $peserta = $this->buatPeserta(
            email: 'peserta2@ut.test',
            nama: 'Siti Aminah',
            instansi: 'Politeknik Negeri Samarinda',
            jurusan: 'Teknik Informatika',
            jenjang: 'D3',
            mulai: now()->subDays(70)->startOfDay(),
            selesai: now()->subDays(10)->endOfDay(),
            divisiDiajukan: 'HRGA',
            target: Peserta::STATUS_SELESAI,
            admin: $admin,
        );

        $this->dokumenTahap1($peserta);
        $this->seleksi($peserta, $admin, 'diterima', 'Sesuai jadwal prakerin perusahaan.');
        $peserta->update([
            'pembimbing_id' => $pembimbing->id,
            'divisi_penempatan' => 'HRGA',
        ]);
        $this->dokumenTahap3($peserta, $admin, 'valid');
        $this->absensiContoh($peserta, $admin, false);

        $nilai = [88, 90, 85, 92, 87];
        PenilaianPembimbing::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'pembimbing_id' => $pembimbing->id,
                'disiplin' => $nilai[0],
                'kerja_sama' => $nilai[1],
                'inisiatif' => $nilai[2],
                'kualitas_kerja' => $nilai[3],
                'komunikasi' => $nilai[4],
                'rata_rata' => array_sum($nilai) / count($nilai),
                'catatan' => 'Peserta menunjukkan sikap disiplin dan kemampuan kerja yang baik selama prakerin.',
            ]
        );

        app(NilaiKehadiranService::class)->hitung($peserta->fresh(['penilaian']));

        return $peserta;
    }

    private function pesertaLain(User $admin): void
    {
        // Tahap 3: menunggu verifikasi dokumen tahap 3
        $tiga = $this->buatPeserta(
            email: 'peserta3@ut.test',
            nama: 'Ahmad Fauzi',
            instansi: 'SMK Negeri 1 Samarinda',
            jurusan: 'Teknik Kendaraan Ringan',
            jenjang: 'SMK',
            mulai: now()->addDays(10)->startOfDay(),
            selesai: now()->addDays(70)->endOfDay(),
            divisiDiajukan: 'Operasional & Pemeliharaan',
            target: Peserta::STATUS_PEMENUHAN,
            admin: $admin,
        );
        $this->dokumenTahap1($tiga);
        $this->seleksi($tiga, $admin, 'diterima', 'Diterima untuk tahap pemenuhan dokumen.');
        $this->dokumenTahap3($tiga, $admin, 'menunggu');

        // Tahap 2: sedang menunggu seleksi administrasi
        $dua = $this->buatPeserta(
            email: 'peserta4@ut.test',
            nama: 'Dewi Lestari',
            instansi: 'SMK Negeri 5 Balikpapan',
            jurusan: 'Akuntansi dan Keuangan Lembaga',
            jenjang: 'SMK',
            mulai: now()->addDays(21)->startOfDay(),
            selesai: now()->addDays(80)->endOfDay(),
            divisiDiajukan: 'Finance & Accounting',
            target: Peserta::STATUS_SELEKSI,
            admin: $admin,
        );
        $this->dokumenTahap1($dua);

        // Tahap 1: baru mendaftar
        $satu = $this->buatPeserta(
            email: 'peserta5@ut.test',
            nama: 'Budi Santoso',
            instansi: 'Universitas Mulawarman',
            jurusan: 'Teknik Mesin',
            jenjang: 'S1',
            mulai: now()->addDays(30)->startOfDay(),
            selesai: now()->addDays(100)->endOfDay(),
            divisiDiajukan: 'Operasional & Pemeliharaan',
            target: Peserta::STATUS_PENDAFTARAN,
            admin: $admin,
        );
        unset($satu);
    }

    private function buatPeserta(
        string $email,
        string $nama,
        string $instansi,
        string $jurusan,
        string $jenjang,
        Carbon $mulai,
        Carbon $selesai,
        string $divisiDiajukan,
        string $target,
        User $admin,
    ): Peserta {
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $nama, 'password' => Hash::make('password'), 'role' => User::ROLE_USER]
        );

        $peserta = Peserta::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nama_lengkap' => $nama,
                'nik' => '64730' . random_int(1000000000, 9999999999),
                'instansi_pendidikan' => $instansi,
                'jurusan' => $jurusan,
                'jenjang' => $jenjang,
                'no_hp' => '0812' . random_int(10000000, 99999999),
                'alamat' => 'Jl. Pahlawan No. ' . random_int(1, 200) . ', Samarinda',
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $selesai->toDateString(),
                'divisi_diajukan' => $divisiDiajukan,
                'status_tahap' => Peserta::STATUS_PENDAFTARAN,
            ]
        );

        app(StatusService::class)->ubah($peserta, $target, $admin, 'Ditetapkan oleh admin saat seeding data demo');

        if ($peserta->wasChanged('status_tahap') === false && $target === Peserta::STATUS_PENDAFTARAN) {
            $peserta->riwayatStatus()->firstOrCreate(
                ['status_baru' => Peserta::STATUS_PENDAFTARAN],
                ['status_lama' => null, 'oleh' => $admin->id, 'catatan' => 'Akun peserta terdaftar']
            );
        }

        return $peserta->fresh();
    }

    private function dokumenTahap1(Peserta $peserta): void
    {
        foreach ([
            'surat_pengantar' => 'Surat Pengantar Instansi',
            'cv' => 'Curriculum Vitae',
            'kartu_identitas' => 'Kartu Pelajar',
            'transkrip_nilai' => 'Transkrip Nilai',
        ] as $jenis => $judul) {
            $this->simpanDokumen($peserta, $jenis, 1, $judul, ['status_verifikasi' => 'menunggu']);
        }
    }

    private function dokumenTahap3(Peserta $peserta, User $admin, string $status): void
    {
        foreach ([
            'bpjs_kesehatan' => 'Kartu BPJS Kesehatan',
            'asuransi_kecelakaan' => 'Kartu Asuransi Kecelakaan Kerja',
            'surat_sehat' => 'Surat Keterangan Sehat',
        ] as $jenis => $judul) {
            $this->simpanDokumen($peserta, $jenis, 3, $judul, [
                'status_verifikasi' => $status,
                'catatan' => $status === 'valid' ? 'Dokumen sesuai ketentuan.' : null,
                'diverifikasi_oleh' => $status === 'valid' ? $admin->id : null,
                'diverifikasi_pada' => $status === 'valid' ? now() : null,
            ]);
        }
    }

    private function simpanDokumen(Peserta $peserta, string $jenis, int $tahap, string $judul, array $opsi = []): Dokumen
    {
        $existing = Dokumen::where('peserta_id', $peserta->id)->where('jenis', $jenis)->first();

        if ($existing) {
            return $existing;
        }

        $html = <<<HTML
            <div style="font-family: DejaVu Sans, sans-serif; padding: 40px;">
                <h2 style="margin-bottom: 6px;">{$judul}</h2>
                <p style="color:#555;">Dokumen contoh untuk data demo sistem magang.</p>
                <hr>
                <p>Nama: {$peserta->nama_lengkap}</p>
                <p>Instansi: {$peserta->instansi_pendidikan}</p>
                <p>Jurusan: {$peserta->jurusan}</p>
            </div>
            HTML;

        $path = 'dokumen/' . $peserta->id . '/' . uniqid($tahap . '-' . $jenis . '-', true) . '.pdf';
        Storage::disk('privat')->put($path, Pdf::loadHTML($html)->setPaper('a4')->output());

        return Dokumen::create(array_merge([
            'peserta_id' => $peserta->id,
            'jenis' => $jenis,
            'tahap' => $tahap,
            'path_file' => $path,
            'nama_asli' => $judul . '.pdf',
        ], $opsi));
    }

    private function seleksi(Peserta $peserta, User $admin, string $keputusan, string $catatan): void
    {
        SeleksiAdministrasi::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'berkas_lengkap' => true,
                'kompetensi_sesuai' => true,
                'periode_sesuai' => true,
                'keputusan' => $keputusan,
                'catatan' => $catatan,
                'dinilai_oleh' => $admin->id,
            ]
        );
    }

    /**
     * Buat absensi harian dari tanggal mulai sampai hari ini (atau tanggal selesai).
     */
    private function absensiContoh(Peserta $peserta, User $admin, bool $siapkanPengajuanPending): void
    {
        $tanggal = $peserta->tanggal_mulai->copy();
        $akhir = now()->startOfDay();

        if ($peserta->tanggal_selesai->lt($akhir)) {
            $akhir = $peserta->tanggal_selesai->copy();
        }

        $hari = 0;

        while ($tanggal->lte($akhir)) {
            $akhirPekan = in_array($tanggal->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
            $libur = HariLibur::where('tanggal', $tanggal->toDateString())->exists();

            if (! $akhirPekan && ! $libur) {
                $hari++;
                $status = Absensi::STATUS_HADIR;
                $keterangan = null;
                $disetujui = null;

                if ($hari % 13 === 0) {
                    $status = Absensi::STATUS_ALPA;
                    $keterangan = 'Tanpa keterangan';
                } elseif ($hari % 11 === 0) {
                    $status = Absensi::STATUS_IZIN;
                    $keterangan = 'Izin keperluan keluarga';
                    $disetujui = true;
                } elseif ($hari % 7 === 0) {
                    $status = Absensi::STATUS_SAKIT;
                    $keterangan = 'Sakit demam, istirahat sesuai anjuran dokter';
                    $disetujui = true;
                }

                Absensi::updateOrCreate(
                    ['peserta_id' => $peserta->id, 'tanggal' => $tanggal->toDateString()],
                    [
                        'jam_masuk' => $status === Absensi::STATUS_HADIR ? sprintf('07:%02d', 40 + ($hari % 20)) : null,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'disetujui' => $disetujui,
                        'disetujui_oleh' => $disetujui !== null ? $admin->id : null,
                    ]
                );
            }

            $tanggal->addDay();
        }

        if ($siapkanPengajuanPending) {
            // Satu pengajuan izin yang belum disetujui untuk mendemokan menu persetujuan admin.
            $pending = $peserta->tanggal_mulai->copy();
            $akhirPending = now()->startOfDay();

            if ($peserta->tanggal_selesai->lt($akhirPending)) {
                $akhirPending = $peserta->tanggal_selesai->copy();
            }

            $kandidat = null;

            while ($pending->lte($akhirPending)) {
                $akhirPekan = in_array($pending->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
                $libur = HariLibur::where('tanggal', $pending->toDateString())->exists();

                if (! $akhirPekan && ! $libur) {
                    $kandidat = $pending->toDateString();
                }

                $pending->addDay();
            }

            Absensi::updateOrCreate(
                ['peserta_id' => $peserta->id, 'tanggal' => $kandidat ?? now()->toDateString()],
                [
                    'jam_masuk' => null,
                    'status' => Absensi::STATUS_IZIN,
                    'keterangan' => 'Izin mengurus keperluan mendesak (contoh pengajuan menunggu persetujuan)',
                    'lampiran' => null,
                    'disetujui' => null,
                    'disetujui_oleh' => null,
                ]
            );
        }

        app(NilaiKehadiranService::class)->hitung($peserta);
    }
}
