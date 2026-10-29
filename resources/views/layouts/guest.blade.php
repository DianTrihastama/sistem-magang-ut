<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-ut-900 text-slate-800">
<div class="min-h-screen flex items-center justify-center p-4 bg-cover bg-center"
     style="background-image: linear-gradient(rgba(17,17,17,.88), rgba(17,17,17,.94)), radial-gradient(circle at 20% 20%, #FDC30022, transparent 60%);">
    <div class="w-full max-w-md">
        <div class="flex items-center justify-center gap-3 mb-6">
            <img src="{{ asset('images/logo-ut.svg') }}" alt="Logo UT" class="w-12 h-12 rounded-xl">
            <div class="text-white">
                <p class="font-bold text-ut-500 leading-tight">Sistem Magang</p>
                <p class="text-xs text-slate-300 leading-tight">PT United Tractors Samarinda</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8">
            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </div>

        <p class="text-center text-xs text-slate-400 mt-4">&copy; {{ date('Y') }} PT United Tractors Samarinda - Prakerin</p>
    </div>
</div>
</body>
</html>
