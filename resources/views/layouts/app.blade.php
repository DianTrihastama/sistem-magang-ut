<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', isset($header) ? strip_tags($header) : 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
@php $currentUser = auth()->user(); @endphp
<div class="min-h-screen flex" x-data="{ open: false }">

    {{-- Sidebar --}}
    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-ut-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 flex flex-col"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-ut-700">
            <img src="{{ asset('images/logo-ut.svg') }}" alt="Logo UT" class="w-10 h-10 rounded-lg">
            <div>
                <p class="font-bold leading-tight text-ut-500">Sistem Magang</p>
                <p class="text-[11px] text-slate-300 leading-tight">PT United Tractors Samarinda</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-sm">
            @php
                $role = $currentUser?->role;
                $routeActive = fn (string $name) => request()->routeIs($name) ? 'bg-ut-500 text-ut-900 font-semibold' : 'text-slate-200 hover:bg-ut-800 hover:text-ut-500';
            @endphp

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('dashboard') }}">
                <span class="w-2 h-2 rounded-full bg-current"></span> Dashboard
            </a>

            @if($role === 'user')
                <a href="{{ route('pendaftaran.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('pendaftaran.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Pendaftaran Tahap 1
                </a>

                @if(in_array(optional($currentUser->peserta)->status_tahap, ['pemenuhan_dokumen', 'aktif', 'selesai']))
                    <a href="{{ route('dokumen.tahap3') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('dokumen.tahap3') }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span> Dokumen Tahap 3
                    </a>
                @endif

                @if(in_array(optional($currentUser->peserta)->status_tahap, ['aktif', 'selesai']))
                    <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('absensi.*') }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span> Absensi
                    </a>
                @endif
            @elseif($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.dashboard') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Statistik
                </a>
                <a href="{{ route('admin.pendaftar.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.pendaftar.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Pendaftar &amp; Seleksi
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.absensi.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Absensi &amp; Izin
                </a>
                <a href="{{ route('admin.akun-peserta.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.akun-peserta.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Akun Peserta
                </a>
                <a href="{{ route('admin.pembimbing.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.pembimbing.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Pembimbing
                </a>
                <a href="{{ route('admin.hari-libur.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.hari-libur.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Hari Libur
                </a>
                <a href="{{ route('admin.pengaturan.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('admin.pengaturan.*') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Pengaturan
                </a>
            @elseif($role === 'pembimbing')
                <a href="{{ route('pembimbing.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('pembimbing.dashboard') }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> Peserta Bimbingan
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ $routeActive('profile.*') }}">
                <span class="w-2 h-2 rounded-full bg-current"></span> Profil Saya
            </a>
        </nav>

        <div class="px-5 py-4 border-t border-ut-700 text-xs text-slate-300">
            <p class="font-semibold text-white text-sm">{{ $currentUser?->name }}</p>
            <p class="uppercase tracking-wide">{{ $currentUser?->role }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg bg-ut-500 text-ut-900 font-semibold hover:bg-ut-400">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div x-show="open" x-cloak class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="open = false"></div>

    {{-- Konten --}}
    <div class="flex-1 min-w-0">
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3">
                <div class="flex items-center gap-3">
                    <button type="button" @click="open = !open" class="lg:hidden p-2 rounded-lg border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span class="sr-only">Buka menu</span>
                    </button>
                    <div class="text-sm sm:text-base font-semibold text-ut-900">
                        @isset($header) {{ $header }} @else PT United Tractors Samarinda @endisset
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs">
                    <span class="px-2 py-1 rounded-full bg-ut-100 text-ut-800 font-semibold uppercase tracking-wide">{{ $currentUser?->role }}</span>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
                    <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
