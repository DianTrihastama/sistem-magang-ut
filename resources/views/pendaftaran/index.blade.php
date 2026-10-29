<x-app-layout>
    <x-slot name="header">
        Pendaftaran Tahap 1 - Data Diri &amp; Berkas
    </x-slot>

    <form action="{{ route('pendaftaran.update') }}" method="POST" enctype="multipart/form-data"
          class="space-y-6" onsubmit="return confirm('Kirim data pendaftaran beserta berkas?');">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <h2 class="font-bold text-ut-900 mb-1">1. Data Diri</h2>
            <p class="text-sm text-slate-500 mb-4">Nama lengkap yang diisi di sini menjadi sumber tunggal surat balasan dan sertifikat.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="nama_lengkap">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap', $peserta->nama_lengkap) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('nama_lengkap')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="nik">NIK (16 digit, opsional)</label>
                    <input id="nik" name="nik" type="text" maxlength="16" value="{{ old('nik', $peserta->nik) }}"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('nik')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="instansi_pendidikan">Asal Instansi Pendidikan <span class="text-rose-500">*</span></label>
                    <input id="instansi_pendidikan" name="instansi_pendidikan" type="text" value="{{ old('instansi_pendidikan', $peserta->instansi_pendidikan) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('instansi_pendidikan')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="jurusan">Jurusan <span class="text-rose-500">*</span></label>
                    <input id="jurusan" name="jurusan" type="text" value="{{ old('jurusan', $peserta->jurusan) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('jurusan')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="jenjang">Jenjang <span class="text-rose-500">*</span></label>
                    <select id="jenjang" name="jenjang" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                        @foreach(['SMK', 'D3', 'S1', 'lainnya'] as $jenjang)
                            <option value="{{ $jenjang }}" @selected(old('jenjang', $peserta->jenjang) === $jenjang)>{{ $jenjang }}</option>
                        @endforeach
                    </select>
                    @error('jenjang')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="no_hp">No. Handphone <span class="text-rose-500">*</span></label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $peserta->no_hp) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('no_hp')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="2" class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">{{ old('alamat', $peserta->alamat) }}</textarea>
                    @error('alamat')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <h2 class="font-bold text-ut-900 mb-1">2. Periode &amp; Bidang Magang Diajukan</h2>
            <p class="text-sm text-slate-500 mb-4">Sesuaikan dengan jadwal yang ditetapkan perusahaan (Senin-Jumat).</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="tanggal_mulai">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', $peserta->tanggal_mulai?->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('tanggal_mulai')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="tanggal_selesai">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai', $peserta->tanggal_selesai?->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('tanggal_selesai')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="divisi_diajukan">Bidang / Divisi Diajukan <span class="text-rose-500">*</span></label>
                    <input id="divisi_diajukan" name="divisi_diajukan" type="text" value="{{ old('divisi_diajukan', $peserta->divisi_diajukan) }}" required
                           placeholder="Contoh: Plant Maintenance"
                           class="w-full rounded-lg border-slate-300 focus:ring-ut-500 focus:border-ut-500">
                    @error('divisi_diajukan')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <h2 class="font-bold text-ut-900 mb-1">3. Unggah Berkas Wajib</h2>
            <p class="text-sm text-slate-500 mb-4">Format PDF/JPG/PNG, ukuran maksimal 2 MB per berkas.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(\App\Services\DokumenService::JENIS_TAHAP_1 as $jenis)
                    @php($existing = $dokumen->get($jenis))
                    <div class="border border-dashed border-slate-300 rounded-xl p-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1" for="{{ $jenis }}">
                            {{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }} <span class="text-rose-500">*</span>
                        </label>

                        @if($existing)
                            <p class="text-xs text-emerald-700 mb-2">
                                Sudah terunggah: <a class="underline" href="{{ route('files.dokumen', $existing) }}" target="_blank">{{ $existing->nama_asli }}</a>
                            </p>
                        @endif

                        <input id="{{ $jenis }}" name="{{ $jenis }}" type="file" accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-ut-100 file:text-ut-800 file:font-semibold hover:file:bg-ut-200">
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan bila tidak ingin mengganti berkas.</p>
                        @error($jenis)<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
            <a href="{{ route('dashboard.peserta') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 text-sm font-semibold text-center">Kembali ke Dashboard</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                Simpan &amp; Kirim Pendaftaran
            </button>
        </div>
    </form>
</x-app-layout>
