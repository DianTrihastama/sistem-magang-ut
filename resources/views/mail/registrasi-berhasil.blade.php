<x-mail-layout judul="Registrasi Berhasil">
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Halo <strong>{{ $user->name }}</strong>,
    </p>
    <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
        Akun Anda pada Sistem Pendaftaran &amp; Manajemen Magang PT United Tractors Samarinda telah berhasil dibuat.
        Silakan masuk menggunakan email: <strong>{{ $user->email }}</strong>.
    </p>

    @if($peserta)
        <p style="margin:0 0 12px;color:#334155;font-size:14px;line-height:1.7;">
            Selanjutnya silakan lengkapi <strong>Tahap 1 - Pendaftaran</strong> berupa data diri dan berkas administrasi
            (surat pengantar instansi, CV, KTP/Kartu Pelajar, dan transkrip nilai) melalui menu <em>Pendaftaran Tahap 1</em>.
        </p>
        <p style="margin:0 0 16px;color:#334155;font-size:14px;line-height:1.7;">
            Status saat ini: <strong>{{ $peserta->statusLabel() }}</strong>.
        </p>
    @endif

    <p style="margin:0 0 16px;">
        <a href="{{ route('login') }}"
           style="display:inline-block;background:#FDC300;color:#111111;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:10px;font-size:14px;">
            Masuk ke Sistem
        </a>
    </p>

    <p style="margin:0;color:#64748b;font-size:13px;line-height:1.7;">
        Hormat kami,<br>HRGA PT United Tractors Samarinda
    </p>
</x-mail-layout>
