<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\NilaiKehadiran;
use App\Models\Peserta;
use Carbon\Carbon;

class NilaiKehadiranService
{
    /**
     * Hitung ulang nilai kehadiran peserta berdasarkan absensi.
     * Basis 100, setiap hari izin/sakit/alpa mengurangi poin sesuai pengaturan.
     */
    public function hitung(Peserta $peserta): NilaiKehadiran
    {
        $poinIzin = PengaturanService::angka('poin_pengurangan_izin', 1);
        $poinAlpa = PengaturanService::angka('poin_pengurangan_alpa', 1);

        $absensi = Absensi::where('peserta_id', $peserta->id)->get();

        $jumlahHadir = $absensi->where('status', Absensi::STATUS_HADIR)->count();
        $jumlahIzin = $absensi->where('status', Absensi::STATUS_IZIN)->count();
        $jumlahSakit = $absensi->where('status', Absensi::STATUS_SAKIT)->count();
        $jumlahAlpa = $absensi->where('status', Absensi::STATUS_ALPA)->count();

        $totalHariKerja = $this->totalHariKerja($peserta);

        $nilai = 100
            - (($jumlahIzin + $jumlahSakit) * $poinIzin)
            - ($jumlahAlpa * $poinAlpa);

        $nilai = max(0, $nilai);

        $hasil = NilaiKehadiran::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'total_hari_kerja' => $totalHariKerja,
                'jumlah_hadir' => $jumlahHadir,
                'jumlah_izin' => $jumlahIzin,
                'jumlah_sakit' => $jumlahSakit,
                'jumlah_alpa' => $jumlahAlpa,
                'nilai' => $nilai,
                'dihitung_pada' => now(),
            ]
        );

        // Nilai akhir ikut diperbarui bila penilaian pembimbing sudah ada.
        if ($peserta->penilaian()->exists()) {
            app(NilaiAkhirService::class)->hitung($peserta->fresh(['penilaian', 'nilaiKehadiran']));
        }

        return $hasil;
    }

    /**
     * Total hari kerja (Senin-Jumat, tanpa hari libur) dalam periode magang.
     */
    public function totalHariKerja(Peserta $peserta): int
    {
        if (! $peserta->tanggal_mulai || ! $peserta->tanggal_selesai) {
            return 0;
        }

        $libur = HariLibur::pluck('tanggal')
            ->map(fn ($t) => Carbon::parse($t)->toDateString())
            ->flip();

        $total = 0;
        $tanggal = $peserta->tanggal_mulai->copy();
        $akhir = $peserta->tanggal_selesai->copy();

        while ($tanggal->lte($akhir)) {
            $isWeekday = in_array($tanggal->dayOfWeek, [Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY], true);

            if ($isWeekday && ! isset($libur[$tanggal->toDateString()])) {
                $total++;
            }

            $tanggal->addDay();
        }

        return $total;
    }
}
