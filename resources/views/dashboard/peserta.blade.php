<x-app-layout>
    <x-slot name="header">
        Dashboard Peserta Magang
    </x-slot>

    {{-- Stepper 3 Tahap --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-bold text-ut-900">Progres Pendaftaran</h2>
                <p class="text-sm text-slate-500">Status saat ini: <span class="font-semibold text-ut-700">{{ $peserta->statusLabel() }}</span></p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full
                @switch($peserta->status_tahap)
                    @case('aktif') bg-emerald-100 text-emerald-700 @break
                    @case('selesai') bg-sky-100 text-sky-700 @break
                    @case('ditolak') bg-rose-100 text-rose-700 @break
                    @default bg-ut-100 text-ut-800
                @endswitch">
                {{ $peserta->statusLabel() }}
            </span>
        </div>

        <ol class="flex flex-col sm:flex-row gap-3 sm:gap-0">
            @php
                $langkah = [
                    ['1', 'Tahap 1 - Pendaftaran', 'Data diri & berkas awal', 1],
                    ['2', 'Tahap 2 - Seleksi Administrasi', 'Peninjauan oleh PIC prakerin', 2],
                    ['3', 'Tahap 3 - Pemenuhan Dokumen', 'BPJS, asuransi, surat sehat', 3],
                ];
            @endphp

            @foreach($langkah as [$no, $judul, $ket, $index])
                @php($aktif = $tahapSaatIni >= $index)
                <li class="flex-1 relative">
                    <div class="flex items-start gap-3 sm:block">
                        <div class="flex sm:items-center">
                            <span class="w-9 h-9 shrink-0 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $aktif ? 'bg-ut-500 text-ut-900' : 'bg-slate-200 text-slate-500' }}">{{ $no }}</span>
                            <span class="hidden sm:block flex-1 h-1 mx-2 mt-4 sm:mt-0 {{ $aktif && $tahapSaatIni > $index ? 'bg-ut-500' : 'bg-slate-200' }}"></span>
                        </div>
                        <div class="sm:mt-3 sm:pr-4">
                            <p class="text-sm font-semibold {{ $aktif ? 'text-ut-900' : 'text-slate-400' }}">{{ $judul }}</p>
                            <p class="text-xs text-slate-500">{{ $ket }}</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>

        @if($peserta->status_tahap === 'ditolak')
            <div class="mt-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
                Pendaftaran Anda ditolak pada seleksi administrasi.
                @if($peserta->seleksi?->catatan) Catatan: {{ $peserta->seleksi->catatan }} @endif
            </div>
        @elseif($peserta->seleksi && $peserta->status_tahap !== 'ditolak')
            <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 text-sm px-4 py-3 text-slate-600">
                Hasil seleksi: <span class="font-semibold text-ut-800">{{ ucfirst($peserta->seleksi->keputusan) }}</span>
                @if($peserta->seleksi->catatan) &mdash; {{ $peserta->seleksi->catatan }} @endif
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Aksi utama --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-bold text-ut-900 mb-3">Langkah Anda</h3>

                <div class="space-y-3">
                    @if(in_array($peserta->status_tahap, ['pendaftaran', 'seleksi_administrasi']))
                        <a href="{{ route('pendaftaran.edit') }}" class="flex items-center justify-between p-4 rounded-xl border border-ut-200 bg-ut-50 hover:bg-ut-100 transition">
                            <div>
                                <p class="font-semibold text-ut-900">Lanjutkan Pendaftaran (Tahap 1)</p>
                                <p class="text-xs text-slate-500">{{ $dokumenTahap1->count() }}/4 berkas terunggah</p>
                            </div>
                            <span class="text-ut-700 font-bold">→</span>
                        </a>
                    @endif

                    @if($peserta->status_tahap === 'pemenuhan_dokumen')
                        <a href="{{ route('dokumen.tahap3') }}" class="flex items-center justify-between p-4 rounded-xl border border-ut-200 bg-ut-50 hover:bg-ut-100 transition">
                            <div>
                                <p class="font-semibold text-ut-900">Lengkapi Dokumen Tahap 3</p>
                                <p class="text-xs text-slate-500">{{ $dokumenTahap3->count() }}/3 berkas terunggah</p>
                            </div>
                            <span class="text-ut-700 font-bold">→</span>
                        </a>
                    @endif

                    @if($bolehAbsensi)
                        <a href="{{ route('absensi.index') }}" class="flex items-center justify-between p-4 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition">
                            <div>
                                <p class="font-semibold text-emerald-800">Absensi Harian</p>
                                <p class="text-xs text-slate-500">Absen masuk, izin, atau sakit</p>
                            </div>
                            <span class="text-emerald-700 font-bold">→</span>
                        </a>
                    @endif

                    @if(!$bolehAbsensi && $peserta->status_tahap !== 'ditolak')
                        <div class="p-4 rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">
                            Menu absensi akan muncul setelah status Anda menjadi <strong>Aktif Magang</strong>.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status dokumen --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-bold text-ut-900 mb-3">Status Dokumen</h3>

                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Tahap 1</p>
                        @foreach(\App\Models\Dokumen::JENIS_LABELS as $jenis => $label)
                            @if(in_array($jenis, ['surat_pengantar', 'cv', 'kartu_identitas', 'transkrip_nilai']))
                                @php($dok = $dokumenTahap1->get($jenis))
                                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                    <span class="text-slate-600">{{ $label }}</span>
                                    <span class="flex items-center gap-3">
                                        @if($dok)
                                            <a href="{{ route('files.dokumen', $dok) }}" target="_blank" class="text-xs text-sky-600 hover:underline">Lihat</a>
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Terunggah</span>
                                        @else
                                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Belum ada</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if(in_array($peserta->status_tahap, ['pemenuhan_dokumen', 'aktif', 'selesai']))
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Tahap 3</p>
                            @foreach(\App\Models\Dokumen::JENIS_LABELS as $jenis => $label)
                                @if(in_array($jenis, ['bpjs_kesehatan', 'asuransi_kecelakaan', 'surat_sehat']))
                                    @php($dok = $dokumenTahap3->get($jenis))
                                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                                        <span class="text-slate-600">{{ $label }}</span>
                                        <span class="flex items-center gap-3">
                                            @if($dok)
                                                <a href="{{ route('files.dokumen', $dok) }}" target="_blank" class="text-xs text-sky-600 hover:underline">Lihat</a>
                                                @if($dok->status_verifikasi === 'valid')
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Valid</span>
                                                @elseif($dok->status_verifikasi === 'tidak_valid')
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">Tidak valid</span>
                                                @else
                                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-ut-100 text-ut-800">Menunggu</span>
                                                @endif
                                            @else
                                                <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Belum ada</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endforeach
                            @php($catatanTolak = $dokumenTahap3->where('status_verifikasi', 'tidak_valid'))
                            @if($catatanTolak->isNotEmpty())
                                <div class="mt-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs px-4 py-3 space-y-1">
                                    @foreach($catatanTolak as $dok)
                                        <p><strong>{{ $dok->labelJenis() }}:</strong> {{ $dok->catatan ?? 'Dokumen ditolak, silakan unggah ulang.' }}</p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kartu samping --}}
        <div class="space-y-6">
            <div class="bg-ut-900 text-white rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ut-500 font-semibold">Data Magang</p>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Nama</dt><dd class="font-semibold text-right">{{ $peserta->nama_lengkap }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Instansi</dt><dd class="text-right">{{ $peserta->instansi_pendidikan }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Jurusan</dt><dd class="text-right">{{ $peserta->jurusan }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Periode</dt>
                        <dd class="text-right">{{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Divisi</dt><dd class="text-right">{{ $peserta->divisi_penempatan ?? $peserta->divisi_diajukan ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-400">Pembimbing</dt><dd class="text-right">{{ $peserta->pembimbing?->user?->name ?? 'Belum ditugaskan' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-bold text-ut-900 mb-3">Dokumen Saya</h3>

                @if($peserta->suratBalasan)
                    <a href="{{ route('dokumen.saya.surat') }}" target="_blank"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-ut-400 mb-2 text-sm">
                        <span>Surat Balasan Magang</span>
                        <span class="text-xs font-semibold text-ut-700">Unduh PDF</span>
                    </a>
                    <p class="text-[11px] text-slate-400 mb-3">Nomor: {{ $peserta->suratBalasan->nomor_surat }}</p>
                @endif

                @if($peserta->sertifikat)
                    <a href="{{ route('dokumen.saya.sertifikat') }}" target="_blank"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-ut-400 mb-2 text-sm">
                        <span>Sertifikat Magang</span>
                        <span class="text-xs font-semibold text-ut-700">Unduh PDF</span>
                    </a>
                    <p class="text-[11px] text-slate-400 mb-3">Nomor: {{ $peserta->sertifikat->nomor_sertifikat }} &middot; Predikat {{ $peserta->nilaiAkhir?->predikat ?? '-' }}</p>
                @endif

                @if(! $peserta->suratBalasan && ! $peserta->sertifikat)
                    <p class="text-sm text-slate-500">Belum ada dokumen yang diterbitkan.</p>
                @endif

                @if($peserta->nilaiAkhir?->nilai_akhir !== null && $peserta->status_tahap === 'selesai')
                    <div class="mt-3 rounded-xl bg-ut-50 border border-ut-200 p-3 text-sm">
                        <p class="text-xs text-slate-500">Nilai Akhir Magang</p>
                        <p class="text-xl font-bold text-ut-800">{{ number_format($peserta->nilaiAkhir->nilai_akhir, 1) }}
                            <span class="text-sm font-semibold">({{ $peserta->nilaiAkhir->predikat }})</span></p>
                    </div>
                @elseif($peserta->status_tahap === 'selesai' || $peserta->nilaiAkhir)
                    <div class="mt-3 rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm text-slate-500">
                        Penilaian: menunggu input pembimbing.
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-bold text-ut-900 mb-2">Riwayat Status</h3>
                <ul class="space-y-3 text-sm">
                    @forelse($peserta->riwayatStatus->take(6) as $riwayat)
                        <li class="border-l-2 border-ut-400 pl-3">
                            <p class="font-semibold text-ut-900 text-xs">{{ \App\Models\Peserta::STATUS_LABELS[$riwayat->status_baru] ?? $riwayat->status_baru }}</p>
                            <p class="text-[11px] text-slate-400">{{ $riwayat->created_at->format('d/m/Y H:i') }}</p>
                            @if($riwayat->catatan)<p class="text-[11px] text-slate-500">{{ $riwayat->catatan }}</p>@endif
                        </li>
                    @empty
                        <li class="text-slate-500 text-xs">Belum ada riwayat.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
