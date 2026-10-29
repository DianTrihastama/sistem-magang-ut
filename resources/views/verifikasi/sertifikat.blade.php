<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Sertifikat - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-2xl">
    <div class="text-center mb-6">
        <img src="{{ asset('images/logo-ut.svg') }}" alt="Logo UT" class="w-14 h-14 mx-auto mb-3 rounded-xl">
        <h1 class="text-xl font-bold text-ut-900">Verifikasi Sertifikat Magang</h1>
        <p class="text-sm text-slate-500">Sistem Pendaftaran &amp; Manajemen Magang - PT United Tractors Samarinda</p>
    </div>

    @if($sertifikat)
        <div class="bg-white rounded-2xl border-2 border-emerald-300 shadow-sm overflow-hidden">
            <div class="bg-emerald-600 text-white px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/20 text-lg">&#10003;</span>
                    <div>
                        <p class="font-bold leading-tight">Sertifikat Tervalidasi</p>
                        <p class="text-xs text-emerald-100">Dokumen asli terdaftar dalam sistem kami</p>
                    </div>
                </div>
                <img src="{{ asset('images/logo-ut.svg') }}" alt="UT" class="w-9 h-9 rounded bg-white p-1">
            </div>

            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Nomor Sertifikat</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $sertifikat->nomor_sertifikat }}</span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Nama Peserta</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $sertifikat->peserta->nama_lengkap }}</span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Instansi</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $sertifikat->peserta->instansi_pendidikan }}</span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Periode Magang</span>
                    <span class="font-semibold text-slate-800 text-right">
                        {{ $sertifikat->peserta->tanggal_mulai?->format('d/m/Y') }} - {{ $sertifikat->peserta->tanggal_selesai?->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Divisi</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $sertifikat->peserta->divisi_penempatan ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Nilai Akhir / Predikat</span>
                    <span class="font-semibold text-slate-800 text-right">
                        @if($sertifikat->peserta->nilaiAkhir)
                            {{ number_format($sertifikat->peserta->nilaiAkhir->nilai_akhir, 2) }} - {{ $sertifikat->peserta->nilaiAkhir->predikat }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Tanggal Terbit</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $sertifikat->tanggal_terbit->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Kode Verifikasi</span>
                    <span class="font-mono font-semibold text-slate-800 text-right">{{ $sertifikat->kode_verifikasi }}</span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border-2 border-rose-300 shadow-sm p-8 text-center">
            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-rose-100 text-rose-600 text-3xl font-bold">&#10007;</span>
            <h2 class="mt-4 text-lg font-bold text-rose-700">Sertifikat Tidak Ditemukan</h2>
            <p class="mt-2 text-sm text-slate-500">
                Kode verifikasi <span class="font-mono font-semibold">{{ $kode }}</span> tidak terdaftar pada sistem kami.
                Pastikan kode yang dimasukkan benar atau hubungi pihak HRGA PT United Tractors Samarinda.
            </p>
        </div>
    @endif

    <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} PT United Tractors Samarinda - Prakerin</p>
</div>
</body>
</html>
