<x-app-layout>
    <x-slot name="header">
        Akun Peserta
    </x-slot>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <form method="GET" action="{{ route('admin.akun-peserta.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau email..."
                   class="w-full sm:w-72 rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
            <button class="px-4 py-2 rounded-lg bg-ut-900 text-ut-500 text-sm font-semibold hover:bg-ut-800">Cari</button>
        </form>

        <a href="{{ route('admin.akun-peserta.create') }}" class="px-5 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400 text-center">
            + Tambah Akun Peserta
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Instansi</th>
                        <th class="text-left px-5 py-3">Jurusan</th>
                        <th class="text-left px-5 py-3">Tahap</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($daftar as $peserta)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $peserta->nama_lengkap }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->user->email }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->instansi_pendidikan }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->jurusan }}</td>
                            <td class="px-5 py-3"><span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $peserta->statusLabel() }}</span></td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.pendaftar.show', $peserta) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-100">Detail</a>
                                <a href="{{ route('admin.akun-peserta.edit', $peserta) }}" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-100">Edit</a>
                                <form action="{{ route('admin.akun-peserta.destroy', $peserta) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus akun peserta ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 text-xs hover:bg-rose-50">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada akun peserta.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>
</x-app-layout>
