<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PembimbingBaruMail;
use App\Models\Pembimbing;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PembimbingController extends Controller
{
    public function index(): View
    {
        return view('admin.pembimbing.index', [
            'daftar' => Pembimbing::with(['user', 'peserta'])->withCount('peserta')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.pembimbing.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nip' => ['nullable', 'string', 'max:30'],
            'divisi' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_PEMBIMBING,
            'no_hp' => $request->no_hp,
        ]);

        Pembimbing::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'divisi' => $request->divisi,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('admin.pembimbing.index')->with('success', 'Akun pembimbing berhasil dibuat.');
    }

    public function edit(Pembimbing $pembimbing): View
    {
        $pembimbing->load('user');

        return view('admin.pembimbing.edit', ['pembimbing' => $pembimbing]);
    }

    public function update(Request $request, Pembimbing $pembimbing): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($pembimbing->user_id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nip' => ['nullable', 'string', 'max:30'],
            'divisi' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
        ]);

        $user = $pembimbing->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $pembimbing->update($request->safe()->only(['nip', 'divisi', 'jabatan']));

        return redirect()->route('admin.pembimbing.index')->with('success', 'Data pembimbing diperbarui.');
    }

    public function destroy(Pembimbing $pembimbing): RedirectResponse
    {
        if ($pembimbing->peserta()->exists()) {
            return back()->with('error', 'Pembimbing masih menangani peserta. Pindahkan peserta terlebih dahulu.');
        }

        $pembimbing->user->delete();

        return redirect()->route('admin.pembimbing.index')->with('success', 'Pembimbing dihapus.');
    }

    /**
     * Penugasan pembimbing & divisi untuk peserta.
     */
    public function tugaskan(Request $request, \App\Models\Peserta $peserta): RedirectResponse
    {
        $request->validate([
            'pembimbing_id' => ['required', 'exists:pembimbing,id'],
            'divisi_penempatan' => ['required', 'string', 'max:255'],
        ], [], [
            'pembimbing_id' => 'pembimbing',
            'divisi_penempatan' => 'divisi penempatan',
        ]);

        $peserta->update($request->safe()->only(['pembimbing_id', 'divisi_penempatan']));

        Mail::to($peserta->pembimbing->user->email)->queue(new PembimbingBaruMail($peserta));

        return back()->with('success', 'Penugasan pembimbing diperbarui.');
    }
}
