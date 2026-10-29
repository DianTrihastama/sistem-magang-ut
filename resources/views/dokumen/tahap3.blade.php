<x-app-layout>
    <x-slot name="header">
        Pemenuhan Dokumen Tahap 3
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 mb-6">
        <h2 class="font-bold text-ut-900 mb-1">Kelengkapan Dokumen Tahap 3</h2>
        <p class="text-sm text-slate-500">
            Unggah 3 dokumen berikut. Seluruh dokumen akan diverifikasi oleh admin.
            Bila ada yang ditolak, silakan unggah ulang.
        </p>
        <p class="text-xs text-slate-400 mt-2">Format PDF/JPG/PNG, maksimal 2 MB.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach(\App\Services\DokumenService::JENIS_TAHAP_3 as $jenis)
            @php($existing = $dokumen->get($jenis))
            <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <h3 class="font-semibold text-ut-900 text-sm leading-snug">{{ \App\Models\Dokumen::JENIS_LABELS[$jenis] }}</h3>
                    @if($existing)
                        @if($existing->status_verifikasi === 'valid')
                            <span class="text-[11px] whitespace-nowrap px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">Valid</span>
                        @elseif($existing->status_verifikasi === 'tidak_valid')
                            <span class="text-[11px] whitespace-nowrap px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-semibold">Ditolak</span>
                        @else
                            <span class="text-[11px] whitespace-nowrap px-2 py-0.5 rounded-full bg-ut-100 text-ut-800 font-semibold">Menunggu</span>
                        @endif
                    @else
                        <span class="text-[11px] whitespace-nowrap px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold">Kosong</span>
                    @endif
                </div>

                @if($existing)
                    <p class="text-xs text-slate-500 mb-3 truncate">{{ $existing->nama_asli }}</p>
                    <a href="{{ route('files.dokumen', $existing) }}" target="_blank" class="text-xs text-sky-600 hover:underline mb-3">Lihat berkas</a>
                    @if($existing->status_verifikasi === 'tidak_valid' && $existing->catatan)
                        <p class="text-xs text-rose-600 mb-3">Catatan admin: {{ $existing->catatan }}</p>
                    @endif
                @else
                    <p class="text-xs text-slate-400 mb-3">Belum diunggah.</p>
                @endif

                <form action="{{ route('dokumen.tahap3.upload', $jenis) }}" method="POST" enctype="multipart/form-data" class="mt-auto space-y-3">
                    @csrf
                    <input name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" required
                           class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-ut-100 file:text-ut-800 file:font-semibold hover:file:bg-ut-200">
                    @error('file')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-ut-500 text-ut-900 text-sm font-bold hover:bg-ut-400">
                        {{ $existing ? 'Ganti / Unggah Ulang' : 'Unggah Berkas' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</x-app-layout>
