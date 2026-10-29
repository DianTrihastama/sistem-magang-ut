<x-app-layout>
    <x-slot name="header">
        Penilaian: {{ $peserta->nama_lengkap }}
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-3">Data Peserta</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $peserta->nama_lengkap }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Instansi</dt><dd class="font-medium text-right">{{ $peserta->instansi_pendidikan }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Jurusan</dt><dd class="font-medium">{{ $peserta->jurusan }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Periode</dt><dd class="font-medium text-right">{{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Divisi</dt><dd class="font-medium">{{ $peserta->divisi_penempatan ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2"><dt class="text-slate-500">Tahap</dt><dd class="font-medium">{{ $peserta->statusLabel() }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-3">Kehadiran &amp; Nilai</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                    <dt class="text-slate-500">Total hari kerja</dt>
                    <dd class="font-medium">{{ $peserta->nilaiKehadiran?->total_hari_kerja ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                    <dt class="text-slate-500">Nilai kehadiran</dt>
                    <dd class="font-medium">{{ $peserta->nilaiKehadiran?->nilai !== null ? number_format($peserta->nilaiKehadiran->nilai, 2) : '-' }}</dd>
                </div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1">
                    <dt class="text-slate-500">Rata-rata penilaian</dt>
                    <dd class="font-medium">{{ $peserta->penilaian?->rata_rata !== null ? number_format($peserta->penilaian->rata_rata, 2) : 'Belum dinilai' }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500">Nilai akhir</dt>
                    <dd class="font-bold text-ut-900 text-base">{{ $peserta->nilaiAkhir?->nilai_akhir !== null ? number_format($peserta->nilaiAkhir->nilai_akhir, 2) : '-' }}</dd>
                </div>
            </dl>

            @if($peserta->nilaiAkhir)
                <div class="mt-3 rounded-xl bg-ut-50 border border-ut-200 px-4 py-3">
                    <p class="text-xs text-slate-500">Predikat</p>
                    <p class="font-bold text-ut-900">{{ $peserta->nilaiAkhir->predikat }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h2 class="font-bold text-ut-900 mb-3">Absensi Terakhir</h2>
            <ul class="space-y-2 text-sm">
                @forelse($peserta->absensi as $absensi)
                    <li class="flex items-center justify-between gap-2 border-b border-slate-100 pb-1">
                        <span class="text-slate-500 text-xs">{{ $absensi->tanggal->format('d/m/Y') }}</span>
                        <span class="text-xs font-semibold
                            {{ match($absensi->status) { 'hadir' => 'text-emerald-600', 'alpa' => 'text-rose-600', default => 'text-amber-600' } }}">
                            {{ \App\Models\Absensi::STATUS_LABELS[$absensi->status] }}
                        </span>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">Belum ada data absensi.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="font-bold text-ut-900">Formulir Penilaian Peserta</h2>
                <p class="text-sm text-slate-500">Skala 0 - 100 per aspek. Rata-rata dihitung otomatis, nilai akhir mengikuti bobot pengaturan sistem.</p>
            </div>
            @if($peserta->penilaian?->file_penilaian)
                <a href="{{ route('files.penilaian', $peserta->penilaian) }}" target="_blank"
                   class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">Lampiran Penilaian (PDF)</a>
            @endif
        </div>

        <form action="{{ route('pembimbing.nilai', $peserta) }}" method="POST" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf

            @foreach([
                'disiplin' => 'Disiplin',
                'kerja_sama' => 'Kerja Sama',
                'inisiatif' => 'Inisiatif',
                'kualitas_kerja' => 'Kualitas Kerja',
                'komunikasi' => 'Komunikasi',
            ] as $kunci => $label)
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ $label }} <span class="text-rose-500">*</span></label>
                    <input type="number" name="{{ $kunci }}" min="0" max="100" required
                           value="{{ old($kunci, $peserta->penilaian?->{$kunci} ?? '') }}"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                </div>
            @endforeach

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Pembimbing</label>
                <textarea name="catatan" rows="3" maxlength="2000"
                          class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm"
                          placeholder="Catatan penilaian, catatan bimbingan, atau rekomendasi...">{{ old('catatan', $peserta->penilaian?->catatan) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Lampiran File Penilaian (PDF, maks 2 MB)</label>
                <input type="file" name="file_penilaian" accept="application/pdf"
                       class="w-full text-sm text-slate-500 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-ut-100 file:text-ut-900 file:text-sm file:font-semibold hover:file:bg-ut-200">
                @if($peserta->penilaian?->file_penilaian)
                    <p class="text-xs text-slate-400 mt-1">Kosongkan bila tidak ingin mengganti lampiran.</p>
                @endif
            </div>

            <div class="md:col-span-5 flex justify-end pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                    Simpan Penilaian
                </button>
            </div>
        </form>

        @if($peserta->penilaian)
            <p class="text-xs text-slate-400 mt-4">Terakhir disimpan: {{ $peserta->penilaian->updated_at->format('d/m/Y H:i') }} oleh {{ $peserta->penilaian->pembimbing?->user?->name ?? 'Pembimbing' }}</p>
        @endif
    </div>
</x-app-layout>
