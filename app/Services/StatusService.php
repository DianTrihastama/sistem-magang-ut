<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\RiwayatStatus;
use App\Models\User;

class StatusService
{
    /**
     * Ubah status tahap peserta sambil mencatat audit trail.
     */
    public function ubah(Peserta $peserta, string $statusBaru, ?User $oleh = null, ?string $catatan = null): Peserta
    {
        if ($peserta->status_tahap !== $statusBaru) {
            RiwayatStatus::create([
                'peserta_id' => $peserta->id,
                'status_lama' => $peserta->status_tahap,
                'status_baru' => $statusBaru,
                'oleh' => $oleh?->id,
                'catatan' => $catatan,
            ]);

            $peserta->status_tahap = $statusBaru;
            $peserta->save();
        }

        return $peserta;
    }
}
