<x-app-layout>
    <x-slot name="header">
        Pengaturan Sistem
    </x-slot>

    <form action="{{ route('admin.pengaturan.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-ut-900 mb-1">Nilai Kehadiran</h2>
                <p class="text-sm text-slate-500 mb-4">Basis nilai kehadiran 100, dikurangi poin per hari ketidakhadiran.</p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['poin_pengurangan_izin'] }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="poin_pengurangan_izin"
                               value="{{ old('poin_pengurangan_izin', $pengaturan['poin_pengurangan_izin'] ?? 1) }}" required
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                        <p class="text-xs text-slate-400 mt-1">Berlaku sama untuk sakit.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['poin_pengurangan_alpa'] }}</label>
                        <input type="number" step="0.01" min="0" max="100" name="poin_pengurangan_alpa"
                               value="{{ old('poin_pengurangan_alpa', $pengaturan['poin_pengurangan_alpa'] ?? 5) }}" required
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-ut-900 mb-1">Bobot Nilai Akhir</h2>
                <p class="text-sm text-slate-500 mb-4">Nilai akhir = (rata-rata pembimbing &times; bobot pembimbing + nilai kehadiran &times; bobot kehadiran) &divide; 100. Total bobot harus 100%.</p>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['bobot_pembimbing'] }}</label>
                        <input type="number" step="1" min="0" max="100" name="bobot_pembimbing"
                               value="{{ old('bobot_pembimbing', $pengaturan['bobot_pembimbing'] ?? 60) }}" required
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['bobot_kehadiran'] }}</label>
                        <input type="number" step="1" min="0" max="100" name="bobot_kehadiran"
                               value="{{ old('bobot_kehadiran', $pengaturan['bobot_kehadiran'] ?? 40) }}" required
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-ut-900 mb-1">Penandatangan Dokumen</h2>
                <p class="text-sm text-slate-500 mb-4">Dicantumkan pada surat balasan dan sertifikat magang.</p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['nama_pic'] }}</label>
                        <input type="text" name="nama_pic" maxlength="255" required
                               value="{{ old('nama_pic', $pengaturan['nama_pic'] ?? '') }}"
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['jabatan_pic'] }}</label>
                        <input type="text" name="jabatan_pic" maxlength="255" required
                               value="{{ old('jabatan_pic', $pengaturan['jabatan_pic'] ?? '') }}"
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-ut-900 mb-1">Penomoran Dokumen</h2>
                <p class="text-sm text-slate-500 mb-4">Nomor urut terakhir yang sudah dipakai (diperbarui otomatis saat generate).</p>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['nomor_surat_terakhir'] }}</label>
                        <input type="number" min="0" name="nomor_surat_terakhir" required
                               value="{{ old('nomor_surat_terakhir', $pengaturan['nomor_surat_terakhir'] ?? 0) }}"
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ $kunci['nomor_sertifikat_terakhir'] }}</label>
                        <input type="number" min="0" name="nomor_sertifikat_terakhir" required
                               value="{{ old('nomor_sertifikat_terakhir', $pengaturan['nomor_sertifikat_terakhir'] ?? 0) }}"
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-3 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</x-app-layout>
