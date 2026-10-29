<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrasiBerhasilMail;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Registrasi khusus peserta magang (role user).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['required', 'string', 'max:20'],
            'instansi_pendidikan' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'no_hp' => $request->no_hp,
        ]);

        $peserta = Peserta::create([
            'user_id' => $user->id,
            'nama_lengkap' => $request->name,
            'instansi_pendidikan' => $request->instansi_pendidikan,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'status_tahap' => Peserta::STATUS_PENDAFTARAN,
        ]);

        $peserta->riwayatStatus()->create([
            'status_lama' => null,
            'status_baru' => Peserta::STATUS_PENDAFTARAN,
            'oleh' => $user->id,
            'catatan' => 'Pendaftaran akun peserta',
        ]);

        event(new Registered($user));

        Mail::to($user->email)->queue(new RegistrasiBerhasilMail($user, $peserta));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
