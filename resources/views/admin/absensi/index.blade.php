<x-app-layout>
    <x-slot name="header">
        Absensi &amp; Pengajuan Izin
    </x-slot>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @php($kartu = [
            ['Total', $rekap['total'], 'bg-slate-100 text-slate-700'],
            ['Hadir', $rekap['hadir'], 'bg-emerald-100 text-emerald-700'],
            ['Izin', $rekap['izin'], 'bg-amber-100 text-amber-700'],
            ['Sakit', $rekap['sakit'], 'bg-sky-100 text-sky-700'],
            ['Alpa', $rekap['alpa'], 'bg-rose-100 text-rose-700'],
            ['Menunggu', $rekap['menunggu'], 'bg-ut-100 text-ut-800'],
        ])
        @foreach($kartu as [$label, $nilai, $warna])
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $label }}</p>
                <p class="text-xl font-bold {{ $warna }} mt-1">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.absensi.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Peserta</label>
                <select name="peserta_id" class="rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    <option value="">Semua peserta</option>
                    @foreach($pesertaList as $p)
                        <option value="{{ $p->id }}" @selected(($filter['peserta_id'] ?? '') == $p->id)>{{ $p->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Bulan</label>
                <input type="month" name="bulan" value="{{ $filter['bulan'] ?? '' }}" class="rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Status</label>
                <select name="status" class="rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Absensi::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(($filter['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600 pb-2">
                <input type="checkbox" name="pending" value="1" {{ !empty($filter['pending']) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-ut-600 focus:ring-ut-500">
                Hanya yang menunggu persetujuan
            </label>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-ut-900 text-ut-500 text-sm font-semibold hover:bg-ut-800">Filter</button>
                <a href="{{ route('admin.absensi.export.excel', ['peserta_id' => $filter['peserta_id'] ?? '', 'bulan' => $filter['bulan'] ?? '']) }}"
                   class="px-4 py-2.5 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">Excel</a>
                <a href="{{ route('admin.absensi.export.pdf', ['peserta_id' => $filter['peserta_id'] ?? '', 'bulan' => $filter['bulan'] ?? '']) }}"
                   class="px-4 py-2.5 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="text-left px-5 py-3">Tanggal</th>
                        <th class="text-left px-5 py-3">Peserta</th>
                        <th class="text-left px-5 py-3">Jam</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Keterangan</th>
                        <th class="text-left px-5 py-3">Persetujuan</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" x-data="{ editId: null }">
                    @forelse($daftar as $absensi)
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600">{{ $absensi->tanggal->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.pendaftar.show', $absensi->peserta) }}" class="font-medium text-slate-800 hover:text-ut-700">{{ $absensi->peserta->nama_lengkap }}</a>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $absensi->jam_masuk ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @php($warna = match($absensi->status) {
                                    'hadir' => 'bg-emerald-100 text-emerald-700',
                                    'izin' => 'bg-amber-100 text-amber-700',
                                    'sakit' => 'bg-sky-100 text-sky-700',
                                    default => 'bg-rose-100 text-rose-700',
                                })
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $warna }}">{{ \App\Models\Absensi::STATUS_LABELS[$absensi->status] }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500 max-w-[240px]">
                                {{ $absensi->keterangan ?: '-' }}
                                @if($absensi->lampiran)
                                    <br><a href="{{ route('files.lampiran', $absensi) }}" target="_blank" class="text-sky-600 hover:underline">Lampiran</a>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs">
                                @if($absensi->disetujui === true)
                                    <span class="text-emerald-600 font-semibold">Disetujui</span>
                                @elseif($absensi->disetujui === false)
                                    <span class="text-rose-600 font-semibold">Ditolak</span>
                                @elseif(in_array($absensi->status, ['izin', 'sakit'], true))
                                    <span class="text-ut-800 font-semibold">Menunggu</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if(in_array($absensi->status, ['izin', 'sakit'], true) && $absensi->disetujui === null)
                                    <form action="{{ route('admin.absensi.setujui', $absensi) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="disetujui" value="1">
                                        <button class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.absensi.setujui', $absensi) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="disetujui" value="0">
                                        <button class="px-2.5 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700">Tolak</button>
                                    </form>
                                @endif

                                <button type="button" @click="editId = editId === {{ $absensi->id }} ? null : {{ $absensi->id }}"
                                        class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-100">Koreksi</button>

                                <form action="{{ route('admin.absensi.destroy', $absensi) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus data absensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 text-xs hover:bg-rose-50">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr x-show="editId === {{ $absensi->id }}" x-cloak>
                            <td colspan="7" class="px-5 py-4 bg-slate-50">
                                <form action="{{ route('admin.absensi.update', $absensi) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Tanggal</label>
                                        <input type="date" name="tanggal" value="{{ $absensi->tanggal->format('Y-m-d') }}" required
                                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Status</label>
                                        <select name="status" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                                            @foreach(\App\Models\Absensi::STATUS_LABELS as $value => $label)
                                                <option value="{{ $value }}" @selected($absensi->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Jam masuk</label>
                                        <input type="time" name="jam_masuk" value="{{ $absensi->jam_masuk }}" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Keterangan</label>
                                        <input type="text" name="keterangan" value="{{ $absensi->keterangan }}" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                                    </div>
                                    <div class="sm:col-span-4 flex justify-end gap-2">
                                        <button type="button" @click="editId = null" class="px-4 py-2 rounded-lg border border-slate-300 text-xs text-slate-600">Batal</button>
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-ut-500 text-ut-900 text-xs font-bold hover:bg-ut-400">Simpan Koreksi</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Tidak ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>
</x-app-layout>
