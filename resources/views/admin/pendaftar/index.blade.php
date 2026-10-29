<x-app-layout>
    <x-slot name="header">
        Manajemen Pendaftar
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 mb-6">
        <form method="GET" action="{{ route('admin.pendaftar.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ $filter['q'] ?? '' }}" placeholder="Cari nama / instansi / jurusan..."
                   class="flex-1 rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">

            <select name="status" class="rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                <option value="">Semua Tahap</option>
                @foreach($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(($filter['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-2.5 rounded-lg bg-ut-900 text-ut-500 text-sm font-semibold hover:bg-ut-800">Filter</button>
            <a href="{{ route('admin.pendaftar.index') }}" class="px-4 py-2.5 rounded-lg border border-slate-300 text-sm text-slate-600 text-center">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Instansi</th>
                        <th class="text-left px-5 py-3">Periode</th>
                        <th class="text-left px-5 py-3">Berkas</th>
                        <th class="text-left px-5 py-3">Tahap</th>
                        <th class="text-left px-5 py-3">Pembimbing</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($daftar as $peserta)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-800">{{ $peserta->nama_lengkap }}</p>
                                <p class="text-xs text-slate-400">{{ $peserta->jurusan }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->instansi_pendidikan }}</td>
                            <td class="px-5 py-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 text-xs">{{ $peserta->dokumen->where('tahap', 1)->count() }}/4</td>
                            <td class="px-5 py-3">
                                @php($warna = match($peserta->status_tahap) {
                                    'aktif' => 'bg-emerald-100 text-emerald-700',
                                    'selesai' => 'bg-indigo-100 text-indigo-700',
                                    'ditolak' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-ut-100 text-ut-800',
                                })
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $warna }}">{{ $peserta->statusLabel() }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500">{{ $peserta->pembimbing?->user?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.pendaftar.show', $peserta) }}"
                                   class="px-3 py-1.5 rounded-lg bg-ut-500 text-ut-900 text-xs font-bold hover:bg-ut-400">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Tidak ada pendaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>
</x-app-layout>
