<x-app-layout>
    <x-slot name="header">
        Detail Pendaftar: {{ $peserta->nama_lengkap }}
    </x-slot>

    @php
        $checklistDokumen = [
            'bpjs_kesehatan' => [
                'Nomor kartu BPJS terbaca jelas',
                'Nama peserta sesuai dengan data pendaftaran',
                'Status kepesertaan aktif / tercantum masa berlaku',
                'Jelas asal instansi pemberi kartu',
            ],
            'asuransi_kecelakaan' => [
                'Tertulis nama peserta sebagai tertanggung',
                'Masa berlaku mencakup seluruh periode prakerin',
                'Jelas perusahaan penerbit polis / asuransi',
                'Terdapat nomor polis / nomor kartu',
            ],
            'surat_sehat' => [
                'Nama peserta sesuai KTP / data pendaftaran',
                'Terdapat hasil pemeriksaan: dinyatakan sehat / jasmani rohani normal',
                'Diterbitkan oleh Poliklinik United Tractors atau rumah sakit rekanan',
                'Terdapat tanda tangan dan stempel dokter',
                'Terdapat tanggal pemeriksaan / masa berlaku surat',
                'Foto peserta (bila disertakan) dan data identitas lengkap',
            ],
        ];
    @endphp

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h2 class="font-bold text-ut-900 text-lg">{{ $peserta->nama_lengkap }}</h2>
                    <p class="text-sm text-slate-500">{{ $peserta->instansi_pendidikan }} &middot; {{ $peserta->jurusan }} &middot; {{ $peserta->jenjang }}</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-ut-100 text-ut-800 whitespace-nowrap">{{ $peserta->statusLabel() }}</span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ $peserta->user->email }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">No. HP</dt><dd class="font-medium">{{ $peserta->no_hp ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">NIK</dt><dd class="font-medium">{{ $peserta->nik ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Alamat</dt><dd class="font-medium text-right">{{ $peserta->alamat ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Periode diajukan</dt><dd class="font-medium">{{ $peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $peserta->tanggal_selesai?->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Divisi diajukan</dt><dd class="font-medium">{{ $peserta->divisi_diajukan ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Divisi penempatan</dt><dd class="font-medium">{{ $peserta->divisi_penempatan ?? '-' }}</dd></div>
                <div class="flex justify-between gap-2 border-b border-slate-100 pb-1"><dt class="text-slate-500">Pembimbing</dt><dd class="font-medium">{{ $peserta->pembimbing?->user?->name ?? 'Belum ditugaskan' }}</dd></div>
            </dl>

            {{-- Penugasan pembimbing --}}
            @if(in_array($peserta->status_tahap, ['pemenuhan_dokumen', 'aktif', 'selesai']))
                <form action="{{ route('admin.peserta.penugasan', $peserta) }}" method="POST" class="mt-5 pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Pembimbing</label>
                        <select name="pembimbing_id" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm" required>
                            @foreach($pembimbingList as $p)
                                <option value="{{ $p->id }}" @selected($peserta->pembimbing_id === $p->id)>{{ $p->user->name }} - {{ $p->divisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Divisi Penempatan</label>
                        <input type="text" name="divisi_penempatan" value="{{ old('divisi_penempatan', $peserta->divisi_penempatan) }}" required
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-ut-900 text-ut-500 text-sm font-semibold hover:bg-ut-800">Simpan Penugasan</button>
                </form>
            @endif
        </div>

        {{-- Dokumen --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold text-ut-900 mb-3">Berkas Tahap 1</h3>
            <ul class="space-y-2 text-sm">
                @foreach(\App\Services\DokumenService::JENIS_TAHAP_1 as $jenis)
                    @php($dok = $peserta->dokumen->firstWhere('jenis', $jenis))
                    <li class="flex items-center justify-between gap-2">
                        <span class="text-slate-600 text-xs">{{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }}</span>
                        @if($dok)
                            <a href="{{ route('files.dokumen', $dok) }}" target="_blank" class="text-xs text-sky-600 hover:underline whitespace-nowrap">Preview</a>
                        @else
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Tidak ada</span>
                        @endif
                    </li>
                @endforeach
            </ul>

            <h3 class="font-bold text-ut-900 mt-5 mb-3">Berkas Tahap 3</h3>
            <ul class="space-y-2 text-sm">
                @foreach(\App\Services\DokumenService::JENIS_TAHAP_3 as $jenis)
                    @php($dok = $peserta->dokumen->firstWhere('jenis', $jenis))
                    <li class="flex items-center justify-between gap-2">
                        <span class="text-slate-600 text-xs">{{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }}</span>
                        @if($dok)
                            <span class="flex items-center gap-2">
                                @if($dok->status_verifikasi === 'valid')<span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Valid</span>
                                @elseif($dok->status_verifikasi === 'tidak_valid')<span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-700">Tidak valid</span>
                                @else<span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">Menunggu</span>@endif
                                <a href="{{ route('files.dokumen', $dok) }}" target="_blank" class="text-xs text-sky-600 hover:underline">Preview</a>
                            </span>
                        @else
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Belum</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Seleksi Administrasi --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-6">
        <h2 class="font-bold text-ut-900 mb-1">Seleksi Administrasi (Tahap 2)</h2>
        <p class="text-sm text-slate-500 mb-4">Centang 3 aspek penilaian, lalu tentukan keputusan.</p>

        @if($peserta->seleksi)
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 mb-4 text-sm">
                <div class="flex flex-wrap gap-4 mb-2">
                    <span class="flex items-center gap-2"><span class="{{ $peserta->seleksi->berkas_lengkap ? 'text-emerald-600' : 'text-rose-500' }}">{{ $peserta->seleksi->berkas_lengkap ? '✔' : '✘' }}</span> Kelengkapan berkas</span>
                    <span class="flex items-center gap-2"><span class="{{ $peserta->seleksi->kompetensi_sesuai ? 'text-emerald-600' : 'text-rose-500' }}">{{ $peserta->seleksi->kompetensi_sesuai ? '✔' : '✘' }}</span> Kompetensi sesuai</span>
                    <span class="flex items-center gap-2"><span class="{{ $peserta->seleksi->periode_sesuai ? 'text-emerald-600' : 'text-rose-500' }}">{{ $peserta->seleksi->periode_sesuai ? '✔' : '✘' }}</span> Periode sesuai</span>
                </div>
                <p>Keputusan: <strong class="text-ut-800">{{ ucfirst($peserta->seleksi->keputusan) }}</strong></p>
                @if($peserta->seleksi->catatan)<p class="text-slate-500 mt-1">Catatan: {{ $peserta->seleksi->catatan }}</p>@endif
                <p class="text-xs text-slate-400 mt-1">Dinilai oleh {{ $peserta->seleksi->dinilaiOleh?->name }} pada {{ $peserta->seleksi->created_at->format('d/m/Y H:i') }}</p>
            </div>
        @endif

        @if($peserta->status_tahap !== 'ditolak' && in_array($peserta->status_tahap, ['pendaftaran', 'seleksi_administrasi', 'pemenuhan_dokumen']))
            <form action="{{ route('admin.seleksi.store', $peserta) }}" method="POST" onsubmit="return confirm('Simpan keputusan seleksi? Pastikan pilihan sudah benar.');"
                  x-data="{ keputusan: '{{ old('keputusan', $peserta->seleksi?->keputusan ?? 'diterima') }}' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    @foreach([
                        'berkas_lengkap' => 'Kelengkapan berkas administrasi',
                        'kompetensi_sesuai' => 'Kesesuaian kompetensi calon peserta dengan jenis kegiatan di perusahaan',
                        'periode_sesuai' => 'Kesesuaian periode prakerin dengan jadwal perusahaan',
                    ] as $kunci => $label)
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 hover:border-ut-400 cursor-pointer">
                            <input type="hidden" name="{{ $kunci }}" value="0">
                            <input type="checkbox" name="{{ $kunci }}" value="1" {{ old($kunci, $peserta->seleksi?->{$kunci}) ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-slate-300 text-ut-600 focus:ring-ut-500">
                            <span class="text-sm text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keputusan <span class="text-rose-500">*</span></label>
                        <select name="keputusan" x-model="keputusan" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm" required>
                            <option value="diterima" @selected(old('keputusan') === 'diterima')>Diterima</option>
                            <option value="revisi" @selected(old('keputusan') === 'revisi')>Perlu Revisi Berkas</option>
                            <option value="ditolak" @selected(old('keputusan') === 'ditolak')>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $peserta->seleksi?->catatan) }}"
                               class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm" placeholder="Catatan untuk peserta">
                    </div>

                    <div id="blok-diterima" class="md:col-span-2">
                        <div x-show="keputusan === 'diterima'" class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-ut-50 border border-ut-200">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pembimbing <span class="text-rose-500">*</span></label>
                                <select name="pembimbing_id" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                                    <option value="">-- pilih pembimbing --</option>
                                    @foreach($pembimbingList as $p)
                                        <option value="{{ $p->id }}" @selected(old('pembimbing_id', $peserta->pembimbing_id) == $p->id)>{{ $p->user->name }} - {{ $p->divisi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Divisi Penempatan <span class="text-rose-500">*</span></label>
                                <input type="text" name="divisi_penempatan" value="{{ old('divisi_penempatan', $peserta->divisi_penempatan) }}"
                                       class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Simpan Keputusan Seleksi</button>
                </div>
            </form>
        @endif
    </div>

    {{-- Verifikasi Tahap 3 --}}
    @if(in_array($peserta->status_tahap, ['pemenuhan_dokumen', 'aktif', 'selesai']))
        <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-6">
            <h2 class="font-bold text-ut-900 mb-1">Verifikasi Dokumen Tahap 3</h2>
            <p class="text-sm text-slate-500 mb-4">Tentukan status valid / tidak valid per dokumen beserta checklist detail dan catatan. Jika seluruh dokumen valid, peserta otomatis menjadi <strong>Aktif Magang</strong>.</p>

            <form action="{{ route('admin.dokumen.verifikasi-semua', $peserta) }}" method="POST">
                @csrf

                <div class="space-y-5">
                    @foreach(\App\Services\DokumenService::JENIS_TAHAP_3 as $index => $jenis)
                        @php($dok = $peserta->dokumen->firstWhere('jenis', $jenis))
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="font-semibold text-slate-800 text-sm">{{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }}</span>
                                    @if($dok)
                                        <a href="{{ route('files.dokumen', $dok) }}" target="_blank" class="text-xs text-sky-600 hover:underline">Preview berkas</a>
                                    @endif
                                </div>

                                @if($dok)
                                    <div class="flex items-center gap-4 text-sm">
                                        <label class="flex items-center gap-2">
                                            <input type="radio" name="status[{{ $index }}]" value="valid" {{ ($dok->status_verifikasi ?? '') === 'valid' ? 'checked' : '' }}
                                                   class="text-emerald-600 focus:ring-emerald-500">
                                            <span>Valid</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="radio" name="status[{{ $index }}]" value="tidak_valid" {{ ($dok->status_verifikasi ?? '') === 'tidak_valid' ? 'checked' : '' }}
                                                   class="text-rose-600 focus:ring-rose-500">
                                            <span>Tidak valid</span>
                                        </label>
                                    </div>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Belum diunggah peserta</span>
                                @endif
                            </div>

                            <input type="hidden" name="jenis[{{ $index }}]" value="{{ $jenis }}">

                            @if($dok)
                                <div class="mb-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Checklist verifikasi {{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }}</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($checklistDokumen[$jenis] as $item)
                                            <label class="flex items-start gap-2 text-sm text-slate-600">
                                                <input type="checkbox" name="checklist[{{ $index }}][]" value="{{ $item }}"
                                                       class="mt-0.5 rounded border-slate-300 text-ut-600 focus:ring-ut-500">
                                                <span>{{ $item }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Catatan</label>
                                    <input type="text" name="catatan[{{ $index }}]" value="{{ old("catatan.$index", $dok->catatan) }}"
                                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500 text-sm"
                                           placeholder="Catatan verifikasi (wajib bila tidak valid)">
                                </div>

                                @if($dok->diverifikasi_pada)
                                    <p class="text-[11px] text-slate-400 mt-2">Terakhir diverifikasi {{ $dok->diverifikasi_pada->format('d/m/Y H:i') }} oleh {{ $dok->diverifikasiOleh?->name ?? '-' }}</p>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-ut-900 text-ut-500 text-sm font-bold hover:bg-ut-800">Simpan Hasil Verifikasi</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Surat balasan & sertifikat --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold text-ut-900 mb-1">Surat Balasan Magang</h3>
            <p class="text-sm text-slate-500 mb-4">Nomor otomatis: <code class="text-xs bg-slate-100 px-1 rounded">XXX/UT-SMD/HRGA/BULAN-ROMAWI/TAHUN</code></p>

            @if($peserta->suratBalasan)
                <p class="text-sm mb-3">Nomor: <strong>{{ $peserta->suratBalasan->nomor_surat }}</strong> ({{ $peserta->suratBalasan->tanggal_surat->format('d/m/Y') }})</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.surat.download', $peserta) }}" class="px-4 py-2 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Unduh PDF</a>
                    <form action="{{ route('admin.surat.kirim', $peserta) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">Kirim ke Email</button>
                    </form>
                </div>
            @else
                <form action="{{ route('admin.surat.generate', $peserta) }}" method="POST" onsubmit="return confirm('Generate surat balasan untuk peserta ini?');">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Generate Surat Balasan</button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold text-ut-900 mb-1">Sertifikat Magang</h3>
            <p class="text-sm text-slate-500 mb-4">Tersedia bila periode magang selesai dan nilai sudah diinput pembimbing.</p>

            @php($bolehSertifikat = $peserta->periodeSelesai() && $peserta->nilaiAkhir?->nilai_akhir !== null)

            @if($peserta->sertifikat)
                <p class="text-sm mb-1">Nomor: <strong>{{ $peserta->sertifikat->nomor_sertifikat }}</strong></p>
                <p class="text-xs text-slate-500 mb-3">Predikat: {{ $peserta->nilaiAkhir?->predikat ?? '-' }} &middot; Nilai akhir {{ $peserta->nilaiAkhir?->nilai_akhir ?? '-' }} &middot; Terbit {{ $peserta->sertifikat->tanggal_terbit->format('d/m/Y') }}</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.sertifikat.download', $peserta) }}" class="px-4 py-2 rounded-lg bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">Unduh PDF</a>
                    <form action="{{ route('admin.sertifikat.kirim', $peserta) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">Kirim ke Email</button>
                    </form>
                </div>
            @else
                <form action="{{ route('admin.sertifikat.generate', $peserta) }}" method="POST" onsubmit="return confirm('Generate sertifikat magang?');">
                    @csrf
                    <button type="submit" @disabled(! $bolehSertifikat)
                            class="px-5 py-2.5 rounded-lg text-sm font-bold {{ $bolehSertifikat ? 'bg-ut-500 text-ut-900 hover:bg-ut-400' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                        Generate Sertifikat
                    </button>
                </form>
                @if(! $bolehSertifikat)
                    <p class="text-xs text-slate-400 mt-2">
                        @if(! $peserta->periodeSelesai())
                            Periode magang belum berakhir ({{ $peserta->tanggal_selesai?->format('d/m/Y') }}).
                        @else
                            Nilai pembimbing belum diinput.
                        @endif
                    </p>
                @endif
            @endif
        </div>
    </div>

    {{-- Riwayat --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-ut-900 mb-3">Riwayat Status (Audit Trail)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wide text-slate-400 border-b border-slate-200">
                    <tr>
                        <th class="text-left py-2 pr-3">Waktu</th>
                        <th class="text-left py-2 pr-3">Perubahan</th>
                        <th class="text-left py-2 pr-3">Oleh</th>
                        <th class="text-left py-2 pr-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peserta->riwayatStatus as $riwayat)
                        <tr>
                            <td class="py-2 pr-3 text-xs text-slate-500 whitespace-nowrap">{{ $riwayat->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 pr-3 text-xs">
                                {{ \App\Models\Peserta::STATUS_LABELS[$riwayat->status_lama] ?? $riwayat->status_lama ?? '-' }}
                                →
                                <strong>{{ \App\Models\Peserta::STATUS_LABELS[$riwayat->status_baru] ?? $riwayat->status_baru }}</strong>
                            </td>
                            <td class="py-2 pr-3 text-xs">{{ $riwayat->oleh?->name ?? 'Sistem' }}</td>
                            <td class="py-2 pr-3 text-xs text-slate-500">{{ $riwayat->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-slate-400 text-xs">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
