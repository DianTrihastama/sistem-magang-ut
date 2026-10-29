<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-xl font-bold text-ut-900">Masuk ke Sistem Magang</h1>
        <p class="text-sm text-slate-500 mt-1">Satu akun untuk Admin, Pembimbing, dan Peserta</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-ut-600 shadow-sm focus:ring-ut-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-slate-500 hover:text-ut-700" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif

            <x-primary-button class="ms-3 !bg-ut-500 !text-ut-900 hover:!bg-ut-400">
                Masuk
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-200 text-center text-sm text-slate-600">
        Belum punya akun peserta?
        <a href="{{ route('register') }}" class="font-semibold text-ut-700 hover:underline">Daftar sebagai Peserta Magang</a>
    </div>
</x-guest-layout>
