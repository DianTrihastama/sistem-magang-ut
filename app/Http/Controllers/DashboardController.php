<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'pembimbing' => redirect()->route('pembimbing.dashboard'),
            default => $this->dashboardPeserta($request),
        };
    }

    private function dashboardPeserta(Request $request): RedirectResponse
    {
        $peserta = Peserta::where('user_id', $request->user()->id)->first();

        if (! $peserta) {
            return redirect()->route('pendaftaran.edit');
        }

        return redirect()->route('dashboard.peserta');
    }
}
