<x-guest-layout>
    <div class="text-center mb-5">
        <h1 class="text-xl font-bold text-ut-900">Registrasi Peserta Magang</h1>
        <p class="text-sm text-slate-500 mt-1">Buat akun untuk mengajukan pendaftaran prakerin</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="no_hp" value="No. Handphone" />
            <x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp" :value="old('no_hp')" required placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="instansi_pendidikan" value="Asal Instansi Pendidikan" />
            <x-text-input id="instansi_pendidikan" class="block mt-1 w-full" type="text" name="instansi_pendidikan" :value="old('instansi_pendidikan')" required placeholder="SMK / Sekolah Tinggi ..." />
            <x-input-error :messages="$errors->get('instansi_pendidikan')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="jurusan" value="Jurusan" />
            <x-text-input id="jurusan" class="block mt-1 w-full" type="text" name="jurusan" :value="old('jurusan')" required placeholder="Teknik Kendaraan Ringan, TKJ, ..." />
            <x-input-error :messages="$errors->get('jurusan')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-slate-500 hover:text-ut-700" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button class="ms-4 !bg-ut-500 !text-ut-900 hover:!bg-ut-400">
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
