<x-app-layout>
    <x-slot name="header">
        Hari Libur
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-1">Tambah Hari Libur</h2>
            <p class="text-sm text-slate-500 mb-4">Tanggal libur tidak dihitung sebagai hari kerja dalam perhitungan nilai kehadiran.</p>

            <form action="{{ route('admin.hari-libur.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" required maxlength="255"
                           placeholder="cth: Hari Kemerdekaan RI"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                    Tambahkan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-4">Daftar Hari Libur</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase tracking-wide text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-2 pr-3">Tanggal</th>
                            <th class="text-left py-2 pr-3">Hari</th>
                            <th class="text-left py-2 pr-3">Keterangan</th>
                            <th class="text-right py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($daftar as $libur)
                            <tr>
                                <td class="py-3 pr-3 whitespace-nowrap">{{ $libur->tanggal->format('d/m/Y') }}</td>
                                <td class="py-3 pr-3 text-slate-500">{{ $libur->tanggal->translatedFormat('l') }}</td>
                                <td class="py-3 pr-3">{{ $libur->keterangan }}</td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('admin.hari-libur.destroy', $libur) }}" method="POST"
                                          onsubmit="return confirm('Hapus hari libur ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 text-xs hover:bg-rose-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400 text-sm">Belum ada hari libur terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $daftar->links() }}</div>
        </div>
    </div>
</x-app-layout>
