<x-app-layout>
    <x-slot name="header">
        Edit Pembimbing: {{ $pembimbing->user->name }}
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.pembimbing.update', $pembimbing) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $pembimbing->user->name) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $pembimbing->user->email) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $pembimbing->user->no_hp) }}" maxlength="20"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $pembimbing->nip) }}" maxlength="30"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="divisi" value="{{ old('divisi', $pembimbing->divisi) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $pembimbing->jabatan) }}" required maxlength="255"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
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
                <a href="{{ route('admin.pembimbing.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm text-slate-600">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Perbarui</button>
            </div>
        </form>
    </div>
</x-app-layout>
