<x-app-layout>
    <x-slot name="header">
        Absensi Magang
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-1">Absen Hari Ini</h2>
            <p class="text-sm text-slate-500 mb-4">{{ now()->translatedFormat('l, d F Y') }}</p>

            @if($bolehAbsen && ! $sudahAbsen)
                <form action="{{ route('absensi.masuk') }}" method="POST" onsubmit="return confirm('Konfirmasi absen masuk sekarang?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-xl bg-emerald-500 text-white font-bold hover:bg-emerald-600">
                        Absen Masuk
                    </button>
                </form>
                <p class="text-[11px] text-slate-400 mt-2">Tersedia satu kali per hari kerja (Senin-Jumat).</p>
            @elseif($sudahAbsen)
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3">
                    Anda sudah melakukan absensi hari ini.
                </div>
            @else
                <div class="rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-sm px-4 py-3">
                    Absensi tidak tersedia (akhir pekan / hari libur / di luar periode magang).
                </div>
            @endif

            <hr class="my-5">

            <h3 class="font-bold text-ut-900 mb-1">Ajukan Izin / Sakit</h3>
            <p class="text-xs text-slate-500 mb-3">Menunggu persetujuan admin. Lampiran surat opsional.</p>

            <form action="{{ route('absensi.ajukan') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1" for="tanggal">Tanggal</label>
                    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', now()->toDateString()) }}" required
                           min="{{ $peserta->tanggal_mulai?->format('Y-m-d') }}"
                           max="{{ $peserta->tanggal_selesai?->format('Y-m-d') }}"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1" for="status">Jenis</label>
                    <select id="status" name="status" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                        <option value="izin" @selected(old('status') === 'izin')>Izin</option>
                        <option value="sakit" @selected(old('status') === 'sakit')>Sakit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1" for="keterangan">Keterangan <span class="text-rose-500">*</span></label>
                    <textarea id="keterangan" name="keterangan" rows="2" required class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">{{ old('keterangan') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1" for="lampiran">Lampiran (opsional, PDF/JPG/PNG maks 2 MB)</label>
                    <input id="lampiran" name="lampiran" type="file" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-xs">
                </div>

                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                    Kirim Pengajuan
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-ut-900">Riwayat Kehadiran</h2>
                <span class="text-xs text-slate-400">Periode {{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-200">
                            <th class="py-2 pr-3">Tanggal</th>
                            <th class="py-2 pr-3">Hari</th>
                            <th class="py-2 pr-3">Jam Masuk</th>
                            <th class="py-2 pr-3">Status</th>
                            <th class="py-2 pr-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $baris)
                            <tr class="border-b border-slate-100">
                                <td class="py-2.5 pr-3 whitespace-nowrap">{{ $baris->tanggal->format('d/m/Y') }}</td>
                                <td class="py-2.5 pr-3">{{ $baris->tanggal->translatedFormat('l') }}</td>
                                <td class="py-2.5 pr-3">{{ $baris->jam_masuk ?? '-' }}</td>
                                <td class="py-2.5 pr-3">
                                    @php($warna = ['hadir' => 'bg-emerald-100 text-emerald-700', 'izin' => 'bg-ut-100 text-ut-800', 'sakit' => 'bg-sky-100 text-sky-700', 'alpa' => 'bg-rose-100 text-rose-700'][$baris->status])
                                    <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $warna }}">{{ \App\Models\Absensi::STATUS_LABELS[$baris->status] }}</span>
                                </td>
                                <td class="py-2.5 pr-3 text-xs text-slate-500">
                                    {{ $baris->keterangan ?? '-' }}
                                    @if($baris->lampiran)
                                        <a class="text-sky-600 underline ml-1" href="{{ route('files.lampiran', $baris) }}" target="_blank">lampiran</a>
                                    @endif
                                    @if(in_array($baris->status, ['izin', 'sakit']) && $baris->disetujui === null)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 ml-1">menunggu</span>
                                    @elseif($baris->disetujui === false)
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-600 ml-1">ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-slate-400 text-sm">Belum ada data absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $riwayat->links() }}</div>

            <p class="text-[11px] text-slate-400 mt-3">Nilai kehadiran dihitung otomatis oleh sistem dan hanya dapat dilihat oleh admin serta pembimbing.</p>
        </div>
    </div>
</x-app-layout>
