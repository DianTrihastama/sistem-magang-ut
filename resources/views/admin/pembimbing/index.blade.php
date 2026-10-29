<x-app-layout>
    <x-slot name="header">
        Data Pembimbing
    </x-slot>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.pembimbing.create') }}" class="px-5 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
            + Tambah Pembimbing
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">NIP</th>
                        <th class="text-left px-5 py-3">Divisi</th>
                        <th class="text-left px-5 py-3">Jabatan</th>
                        <th class="text-left px-5 py-3">Peserta</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($daftar as $pembimbing)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $pembimbing->user->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $pembimbing->user->email }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $pembimbing->nip ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $pembimbing->divisi }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $pembimbing->jabatan }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-ut-100 text-ut-800 font-semibold">{{ $pembimbing->peserta_count }} peserta</span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.pembimbing.edit', $pembimbing) }}"
                                   class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-100">Edit</a>
                                <form action="{{ route('admin.pembimbing.destroy', $pembimbing) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus pembimbing ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 text-xs hover:bg-rose-50">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada pembimbing.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>
</x-app-layout>
