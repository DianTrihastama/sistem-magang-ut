<?php

namespace App\Policies;

use App\Models\Peserta;
use App\Models\User;

class PesertaPolicy
{
    /**
     * Melihat data peserta: admin, pemilik, atau pembimbing terkait.
     */
    public function view(User $user, Peserta $peserta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPeserta()) {
            return $user->id === $peserta->user_id;
        }

        if ($user->isPembimbing()) {
            return $peserta->pembimbing_id === $user->pembimbing?->id;
        }

        return false;
    }

    /**
     * Mengelola data peserta (seleksi, verifikasi, koreksi).
     */
    public function manage(User $user, Peserta $peserta): bool
    {
        return $user->isAdmin();
    }

    /**
     * Melihat / menginput nilai peserta.
     */
    public function nilai(User $user, Peserta $peserta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPembimbing() && $peserta->pembimbing_id === $user->pembimbing?->id;
    }
}
