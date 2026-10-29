<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Balasan Magang - {{ $peserta->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .sheet { padding: 24px 32px; }

        .kop { border-bottom: 3px double #111; padding-bottom: 10px; margin-bottom: 4px; display: table; width: 100%; }
        .kop-left { display: table-cell; vertical-align: middle; width: 90px; }
        .kop-right { display: table-cell; vertical-align: middle; }
        .kop-left img { width: 74px; }
        .kop-right h1 { font-size: 17px; letter-spacing: 1px; text-transform: uppercase; }
        .kop-right h2 { font-size: 13px; font-weight: normal; margin-top: 2px; }
        .kop-right p { font-size: 10.5px; line-height: 1.5; color: #333; }

        .nomor { text-align: right; font-size: 12px; margin-top: 6px; }
        .perihal { text-align: center; margin: 22px 0 4px; font-size: 13px; text-decoration: underline; font-weight: bold; }
        .isi { text-align: justify; line-height: 1.7; margin-top: 16px; }
        .isi p { margin-bottom: 10px; }

        table.data { width: 100%; border-collapse: collapse; margin: 12px 0 16px; }
        table.data td { padding: 5px 8px; vertical-align: top; }
        table.data td.label { width: 190px; }
        table.data td.batas { width: 14px; text-align: center; color: #111; }
        table.data td.nilai { border-bottom: 1px dotted #999; }

        .ttd { margin-top: 34px; width: 100%; }
        .ttd table { width: 100%; border-collapse: collapse; }
        .ttd td { vertical-align: top; }
        .ttd td.kanan { width: 240px; text-align: center; }
        .ttd .nama { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        .ttd .nip { font-size: 11px; margin-top: 2px; }

        .footer { margin-top: 28px; font-size: 9.5px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>
<div class="sheet">
    <div class="kop">
        <div class="kop-left">
            <img src="{{ public_path('images/logo-ut.svg') }}" alt="Logo UT">
        </div>
        <div class="kop-right">
            <h1>PT United Tractors Samarinda</h1>
            <h2>Human Resources General Affairs</h2>
            <p>
                Jl. Mulawarman No. 1, Samarinda, Kalimantan Timur 75117 &middot; Telp. (0541) 730123<br>
                Email: hrga.samarinda@unitedtractors.co.id
            </p>
        </div>
    </div>

    <p class="nomor">Nomor: {{ $nomorSurat }}<br>Tanggal: {{ \Carbon\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}</p>

    <p class="perihal">SURAT BALASAN PENERIMAAN PESERTA PRAKTIK KERJA INDUSTRI</p>

    <div class="isi">
        <p>Dengan ini kami menyatakan bahwa mahasiswa/mahasiswi berikut:</p>

        <table class="data">
            <tr>
                <td class="label">Nama Lengkap</td><td class="batas">:</td><td class="nilai">{{ $peserta->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Kependudukan</td><td class="batas">:</td><td class="nilai">{{ $peserta->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Asal Instansi Pendidikan</td><td class="batas">:</td><td class="nilai">{{ $peserta->instansi_pendidikan }}</td>
            </tr>
            <tr>
                <td class="label">Jurusan / Program Studi</td><td class="batas">:</td><td class="nilai">{{ $peserta->jurusan }} ({{ $peserta->jenjang }})</td>
            </tr>
            <tr>
                <td class="label">Periode Prakerin</td><td class="batas">:</td>
                <td class="nilai">{{ $peserta->tanggal_mulai?->translatedFormat('d F Y') }} s.d. {{ $peserta->tanggal_selesai?->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Penempatan Divisi</td><td class="batas">:</td><td class="nilai">{{ $peserta->divisi_penempatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pembimbing Lapangan</td><td class="batas">:</td>
                <td class="nilai">{{ $peserta->pembimbing?->user?->name ?? '-' }} ({{ $peserta->pembimbing?->jabatan ?? '-' }})</td>
            </tr>
        </table>

        <p>dinyatakan <strong>diterima</strong> untuk melaksanakan kegiatan praktik kerja industri (prakerin) di PT United Tractors Samarinda sesuai periode dan penempatan di atas, dengan ketentuan:</p>
        <p>
            a. Peserta wajib mematuhi seluruh peraturan, tata tertib, dan prosedur keselamatan kerja yang berlaku di lingkungan perusahaan;<br>
            b. Peserta wajib mengikuti proses pembelajaran dan bimbingan dari pembimbing lapangan yang ditunjuk;<br>
            c. Peserta wajib menyelesaikan seluruh rangkaian kegiatan hingga periode berakhir dan memenuhi persyaratan administrasi kegiatan.
        </p>
        <p>Demikian surat ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="ttd">
        <table>
            <tr>
                <td>Samarinda, {{ \Carbon\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}</td>
                <td class="kanan">Hormat kami,<br>{{ $jabatanPic }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="kanan">
                    <div class="nama">{{ $namaPic }}</div>
                    <div class="nip">Human Resources General Affairs</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini diterbitkan melalui Sistem Pendaftaran &amp; Manajemen Magang PT United Tractors Samarinda.
        Keaslian surat dapat dipastikan melalui bagian HRGA perusahaan.
    </div>
</div>
</body>
</html>
