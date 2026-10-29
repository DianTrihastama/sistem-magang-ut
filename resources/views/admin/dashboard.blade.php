<x-app-layout>
    <x-slot name="header">
        Admin - Ringkasan Sistem
    </x-slot>

    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        @php($kartu = [
            ['Total Pendaftar', $stats['total'], 'bg-ut-500 text-ut-900'],
            ['Seleksi Administrasi', $stats['seleksi'], 'bg-amber-100 text-amber-800'],
            ['Pemenuhan Dokumen', $stats['pemenuhan'], 'bg-sky-100 text-sky-800'],
            ['Aktif Magang', $stats['aktif'], 'bg-emerald-100 text-emerald-800'],
            ['Selesai', $stats['selesai'], 'bg-indigo-100 text-indigo-800'],
            ['Ditolak', $stats['ditolak'], 'bg-rose-100 text-rose-800'],
        ])
        @foreach($kartu as [$label, $nilai, $warna])
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-bold {{ $warna }} mt-1">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-4">Grafik Pendaftar per Tahap</h2>

            <div class="space-y-3">
                @foreach($grafik as $baris)
                    @php($persen = round(($baris['jumlah'] / $maks) * 100))
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">{{ $baris['label'] }}</span>
                            <span class="font-semibold text-ut-800">{{ $baris['jumlah'] }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-ut-500 transition-all" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-3">Pendaftar Terbaru</h2>
            <ul class="space-y-3">
                @forelse($terbaru as $peserta)
                    <li>
                        <a href="{{ route('admin.pendaftar.show', $peserta) }}" class="flex items-center justify-between gap-2 hover:bg-slate-50 rounded-lg px-2 py-2 -mx-2">
                            <span class="text-sm font-medium text-slate-700 truncate">{{ $peserta->nama_lengkap }}</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 whitespace-nowrap">{{ $peserta->statusLabel() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">Belum ada pendaftar.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
