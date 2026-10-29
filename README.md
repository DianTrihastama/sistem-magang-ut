# Sistem Pendaftaran & Manajemen Magang (Prakerin)

Aplikasi web untuk mengelola proses Praktik Kerja Industri (Prakerin) / magang di
**PT United Tractors Samarinda** — mulai dari pendaftaran peserta, seleksi
administrasi, pemenuhan dokumen, absensi harian, penilaian, hingga penerbitan
surat balasan dan sertifikat yang dapat diverifikasi publik.

Dibangun dengan **Laravel 11**, **Breeze (Blade + Alpine.js)** dan **Tailwind CSS**.

## Fitur

### Peserta
- Registrasi akun & login.
- Mengisi data pendaftaran (instansi, jurusan, jenjang, periode, divisi diajukan).
- Mengunggah 4 dokumen Tahap 1: surat pengantar, CV, kartu identitas, transkrip nilai.
- Memantau status tahapan dan mengunggah 3 dokumen Tahap 3: BPJS kesehatan,
  asuransi kecelakaan kerja, surat sehat.
- Absensi harian (jam masuk) serta pengajuan **izin/sakit** dengan lampiran.
- Melihat nilai kehadiran, nilai pembimbing, dan nilai akhir + predikat.
- Mengunduh surat balasan dan sertifikat.

### Admin (HRGA)
- Verifikasi dokumen Tahap 1 & Tahap 3 (valid / tidak valid + catatan).
- Seleksi administrasi (berkas lengkap, kompetensi, periode) dengan keputusan
  diterima / ditolak / revisi.
- Menugaskan pembimbing dan divisi penempatan.
- Rekap & koreksi absensi, serta persetujuan pengajuan izin/sakit.
- Mengelola hari libur, pengaturan bobot nilai, dan data akun peserta/pembimbing.
- Menerbitkan surat balasan dan sertifikat (PDF) bernomor otomatis.
- Dashboard statistik.

### Pembimbing
- Melihat daftar peserta bimbingan.
- Menilai peserta pada 5 aspek (disiplin, kerja sama, inisiatif, kualitas kerja,
  komunikasi), mengunggah file penilaian, dan memberi catatan.

### Publik
- Halaman verifikasi sertifikat tanpa login melalui kode unik / QR di sertifikat.

## Alur status peserta

```
pendaftaran -> seleksi_administrasi -> pemenuhan_dokumen -> aktif -> selesai
                                                \-> ditolak
```

## Persyaratan

- PHP >= 8.2 dengan ekstensi `gd`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- Composer 2.x
- MySQL / MariaDB
- Node.js 18+ dan npm

> **Catatan QR:** QR code sertifikat dibuat dalam format **SVG** (bukan PNG) agar
> tidak memerlukan ekstensi PHP `imagick`. Hanya perlu dukungan GD/SVG yang tersedia
> secara default pada XAMPP/Laragon.

## Instalasi

### 1. Ambil dependensi & salin konfigurasi

```bash
composer install
npm install
cp .env.example .env        # Windows (CMD): copy .env.example .env
php artisan key:generate
```

### 2. Buat database

Buat database `sistem_magang` (mis. via phpMyAdmin atau CLI):

```sql
CREATE DATABASE sistem_magang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Sesuaikan kredensial pada `.env`:

```env
APP_NAME="Sistem Magang UT"
APP_TIMEZONE=Asia/Makassar
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_magang
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & seed data demo

```bash
php artisan migrate --seed
```

### 4. Build aset frontend

```bash
npm run build          # produksi
# atau untuk pengembangan:
npm run dev
```

### 5. Jalankan aplikasi

```bash
php artisan serve
```

Buka <http://127.0.0.1:8000>.

Antrian (queue) digunakan untuk pengiriman email notifikasi. Saat pengembangan
jalankan worker:

```bash
php artisan queue:work
```

## Akun demo

Semua akun demo menggunakan password: `password`.

| Peran      | Email                    | Keterangan                                |
| ---------- | ------------------------ | ----------------------------------------- |
| Admin      | `admin@ut.test`          | Administrator HRGA / PIC prakerin         |
| Pembimbing | `pembimbing@ut.test`     | Budi Hartono — IT Pengembangan Sistem     |
| Pembimbing | `pembimbing2@ut.test`    | Sri Wahyuni — HRGA                        |
| Peserta    | `peserta@ut.test`        | Rizky Pratama — status **aktif**          |
| Peserta    | `peserta2@ut.test`       | Siti Aminah — status **selesai**          |
| Peserta    | `peserta3@ut.test`       | Ahmad Fauzi — menunggu verifikasi Tahap 3 |
| Peserta    | `peserta4@ut.test`       | Dewi Lestari — menunggu seleksi administrasi |
| Peserta    | `peserta5@ut.test`       | Budi Santoso — baru mendaftar (Tahap 1)   |

## Pengujian

```bash
php artisan test
```

Pengujian memakai database terpisah agar **tidak menghapus data development**.
Pastikan database `sistem_magang_test` sudah dibuat terlebih dahulu:

```sql
CREATE DATABASE sistem_magang_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Konfigurasi sudah diatur di `phpunit.xml` (`DB_DATABASE=sistem_magang_test`).
Suite mencakup autentikasi Breeze, registrasi, profil, serta alur end-to-end
pendaftaran → seleksi → dokumen → absensi → penilaian → sertifikat
(`tests/Feature/AlurMagangTest.php`).

## Struktur singkat

```
app/
  Http/Controllers/          # controller per peran (Admin/, dan publik)
  Http/Requests/             # validasi form
  Http/Middleware/           # EnsureRole (middleware alias "role")
  Models/                    # Eloquent: Peserta, Dokumen, Absensi, dll.
  Services/                  # logika inti: Dokumen, Absensi, Nilai, Surat, Sertifikat
resources/views/             # Blade: dashboard, pendaftaran, admin, pdf, mail
routes/web.php               # seluruh rute aplikasi
database/migrations/         # skema database
database/seeders/            # data demo
docs/ERD.md                  # Entity Relationship Diagram
```

## Dokumentasi model data

Lihat [`docs/ERD.md`](docs/ERD.md) untuk Entity Relationship Diagram (Mermaid)
beserta alur status dan relasi antar tabel.

## Lisensi

Aplikasi ini dikembangkan untuk keperluan internal PT United Tractors Samarinda.
