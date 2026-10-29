<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peserta;
use App\Models\User;
use App\Services\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AkunPesertaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Peserta::with('user')->latest();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($b) => $b->where('nama_lengkap', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$q}%")));
        }

        return view('admin.akun-peserta.index', [
            'daftar' => $query->paginate(10)->withQueryString(),
            'q' => $request->string('q'),
        ]);
    }

    public function create(): View
    {
        return view('admin.akun-peserta.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'instansi_pendidikan' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'in:SMK,D3,S1,lainnya'],
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
            'jenjang' => $request->jenjang,
            'no_hp' => $request->no_hp,
            'status_tahap' => Peserta::STATUS_PENDAFTARAN,
        ]);

        app(StatusService::class)->ubah($peserta, Peserta::STATUS_PENDAFTARAN, $request->user(), 'Akun peserta dibuat oleh admin');

        return redirect()->route('admin.akun-peserta.index')->with('success', 'Akun peserta berhasil dibuat.');
    }

    public function edit(Peserta $peserta): View
    {
        $peserta->load('user');

        return view('admin.akun-peserta.edit', ['peserta' => $peserta]);
    }

    public function update(Request $request, Peserta $peserta): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $peserta->user_id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'instansi_pendidikan' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'in:SMK,D3,S1,lainnya'],
        ]);

        $user = $peserta->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $peserta->update([
            'nama_lengkap' => $request->name,
            'instansi_pendidikan' => $request->instansi_pendidikan,
            'jurusan' => $request->jurusan,
            'jenjang' => $request->jenjang,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.akun-peserta.index')->with('success', 'Data peserta diperbarui.');
    }

    public function destroy(Peserta $peserta): RedirectResponse
    {
        if (in_array($peserta->status_tahap, [Peserta::STATUS_AKTIF, Peserta::STATUS_SELESAI], true)) {
            return back()->with('error', 'Peserta aktif/selesai tidak dapat dihapus.');
        }

        $peserta->user->delete();

        return redirect()->route('admin.akun-peserta.index')->with('success', 'Akun peserta dihapus.');
    }
}
