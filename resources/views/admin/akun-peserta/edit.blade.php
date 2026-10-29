<x-app-layout>
    <x-slot name="header">
        Edit Akun Peserta: {{ $peserta->nama_lengkap }}
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.akun-peserta.update', $peserta) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $peserta->nama_lengkap) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $peserta->user->email) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $peserta->no_hp) }}" maxlength="20"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Instansi Pendidikan <span class="text-rose-500">*</span></label>
                    <input type="text" name="instansi_pendidikan" value="{{ old('instansi_pendidikan', $peserta->instansi_pendidikan) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                    <input type="text" name="jurusan" value="{{ old('jurusan', $peserta->jurusan) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenjang <span class="text-rose-500">*</span></label>
                    <select name="jenjang" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm" required>
                        @foreach(['SMK', 'D3', 'S1', 'lainnya'] as $jenjang)
                            <option value="{{ $jenjang }}" @selected(old('jenjang', $peserta->jenjang) === $jenjang)>{{ $jenjang }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diganti"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.akun-peserta.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm text-slate-600">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Perbarui</button>
            </div>
        </form>
    </div>
</x-app-layout>
