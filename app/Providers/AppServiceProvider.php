<?php

namespace App\Providers;

use App\Models\Peserta;
use App\Policies\PesertaPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(Peserta::class, PesertaPolicy::class);

        // Admin penerima notifikasi internal (input nilai, dsb.)
        Gate::define('akses-admin', fn ($user) => $user?->isAdmin());
    }
}
