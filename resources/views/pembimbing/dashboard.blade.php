<x-app-layout>
    <x-slot name="header">
        Peserta Bimbingan
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="font-bold text-ut-900">{{ $pembimbing->user->name }}</p>
            <p class="text-sm text-slate-500">{{ $pembimbing->jabatan }} &middot; {{ $pembimbing->divisi }}</p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-ut-800">{{ $daftar->count() }}</p>
            <p class="text-xs text-slate-500">Peserta dibimbing</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="text-left px-5 py-3">Peserta</th>
                        <th class="text-left px-5 py-3">Periode</th>
                        <th class="text-left px-5 py-3">Divisi</th>
                        <th class="text-left px-5 py-3">Tahap</th>
                        <th class="text-left px-5 py-3">Kehadiran</th>
                        <th class="text-left px-5 py-3">Penilaian</th>
                        <th class="text-left px-5 py-3">Nilai Akhir</th>
                        <th class="text-right px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($daftar as $peserta)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-800">{{ $peserta->nama_lengkap }}</p>
                                <p class="text-xs text-slate-400">{{ $peserta->instansi_pendidikan }}</p>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500 whitespace-nowrap">
                                {{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->divisi_penempatan ?? '-' }}</td>
                            <td class="px-5 py-3"><span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $peserta->statusLabel() }}</span></td>
                            <td class="px-5 py-3 text-slate-600">{{ $peserta->nilaiKehadiran?->nilai !== null ? number_format($peserta->nilaiKehadiran->nilai, 2) : '-' }}</td>
                            <td class="px-5 py-3">
                                @if($peserta->penilaian)
                                    <span class="text-emerald-600 font-semibold">{{ number_format($peserta->penilaian->rata_rata, 2) }}</span>
                                @else
                                    <span class="text-ut-800 font-semibold">Belum</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-bold text-ut-900">{{ $peserta->nilaiAkhir?->nilai_akhir !== null ? number_format($peserta->nilaiAkhir->nilai_akhir, 2) : '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('pembimbing.show', $peserta) }}"
                                   class="px-3 py-1.5 rounded-lg bg-ut-500 text-ut-900 text-xs font-bold hover:bg-ut-400">Beri Nilai</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">Belum ada peserta yang ditugaskan kepada Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
