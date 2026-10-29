<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat Magang - {{ $peserta->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #111; }

        .sertifikat { width: 100%; position: relative; padding: 22px; }

        .border-1 { border: 6px solid #FDC300; }
        .border-2 { border: 2px solid #111; padding: 22px 30px 26px; }

        .kop { display: table; width: 100%; border-bottom: 3px solid #111; padding-bottom: 8px; }
        .kop-left { display: table-cell; width: 84px; vertical-align: middle; }
        .kop-left img { width: 70px; }
        .kop-right { display: table-cell; vertical-align: middle; text-align: right; }
        .kop-right .nama-perusahaan { font-size: 16px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .kop-right .unit { font-size: 11px; }
        .kop-right .alamat { font-size: 9.5px; color: #333; }

        .judul { text-align: center; margin-top: 26px; }
        .judul .label { font-size: 34px; font-weight: bold; letter-spacing: 6px; text-transform: uppercase; }
        .judul .nomor { font-size: 11px; margin-top: 4px; color: #333; }

        .diberikan { text-align: center; font-size: 13px; margin-top: 20px; }
        .nama-peserta { text-align: center; font-size: 30px; font-weight: bold; margin-top: 8px; text-transform: uppercase; }
        .garis { width: 60%; border-bottom: 2px solid #FDC300; margin: 8px auto 0; }

        .isi { text-align: center; font-size: 12.5px; line-height: 1.8; margin-top: 16px; }
        .isi strong { font-size: 13.5px; }

        table.ringkas { width: 68%; border-collapse: collapse; margin: 16px auto 0; font-size: 11.5px; }
        table.ringkas td { padding: 4px 10px; border-bottom: 1px dotted #999; }
        table.ringkas td.k { width: 46%; text-align: left; color: #444; }
        table.ringkas td.v { text-align: right; font-weight: bold; }

        .bawah { margin-top: 24px; }
        .bawah table { width: 100%; border-collapse: collapse; }
        .bawah td { vertical-align: top; font-size: 11.5px; }
        .bawah td.kanan { width: 250px; text-align: center; }
        .bawah .nama { margin-top: 44px; font-weight: bold; text-decoration: underline; font-size: 13px; }
        .bawah .nip { margin-top: 2px; }

        .qr { margin-top: 10px; }
        .qr img { width: 92px; height: 92px; }
        .qr .kode { font-size: 8.5px; margin-top: 3px; font-family: Courier, monospace; }
        .qr .url { font-size: 8px; color: #444; }

        .verif-kiri { font-size: 9.5px; color: #444; line-height: 1.6; }
        .verif-kiri .nomor { font-weight: bold; font-size: 11px; color: #111; }
    </style>
</head>
<body>
<div class="sertifikat">
    <div class="border-1">
        <div class="border-2">
            <div class="kop">
                <div class="kop-left">
                    <img src="{{ public_path('images/logo-ut.svg') }}" alt="Logo UT">
                </div>
                <div class="kop-right">
                    <div class="nama-perusahaan">PT United Tractors Samarinda</div>
                    <div class="unit">Human Resources General Affairs</div>
                    <div class="alamat">Jl. Mulawarman No. 1, Samarinda, Kalimantan Timur 75117</div>
                </div>
            </div>

            <div class="judul">
                <div class="label">Sertifikat</div>
                <div class="label" style="font-size:17px; letter-spacing:3px;">Praktik Kerja Industri</div>
                <div class="nomor">Nomor: {{ $nomorSertifikat }}</div>
            </div>

            <div class="diberikan kepada:</div>
            <div class="nama-peserta">{{ $peserta->nama_lengkap }}</div>
            <div class="garis"></div>

            <div class="isi">
                atas nama PT United Tractors Samarinda menerbitkan sertifikat ini sebagai bukti bahwa yang bersangkutan
                telah menyelesaikan kegiatan <strong>Praktik Kerja Industri (Prakerin)</strong> dengan baik pada:<br>
                <strong>{{ $peserta->instansi_pendidikan }}</strong> &mdash; {{ $peserta->jurusan }} ({{ $peserta->jenjang }})
            </div>

            <table class="ringkas">
                <tr>
                    <td class="k">Periode Kegiatan</td>
                    <td class="v">{{ $peserta->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $peserta->tanggal_selesai?->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="k">Divisi Penempatan</td>
                    <td class="v">{{ $peserta->divisi_penempatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="k">Pembimbing Lapangan</td>
                    <td class="v">{{ $peserta->pembimbing?->user?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="k">Nilai Akhir</td>
                    <td class="v">{{ number_format($nilaiAkhir->nilai_akhir, 2) }}</td>
                </tr>
                <tr>
                    <td class="k">Predikat</td>
                    <td class="v">{{ $nilaiAkhir->predikat }}</td>
                </tr>
                <tr>
                    <td class="k">Tanggal Penerbitan</td>
                    <td class="v">{{ \Carbon\Carbon::parse($tanggalTerbit)->translatedFormat('d F Y') }}</td>
                </tr>
            </table>

            <div class="bawah">
                <table>
                    <tr>
                        <td>
                            <div class="verif-kiri">
                                <span class="nomor">{{ $kodeVerifikasi }}</span><br>
                                Kode verifikasi keaslian sertifikat.<br>
                                Akses: {{ $urlVerifikasi }}
                            </div>
                        </td>
                        <td class="kanan">
                            Samarinda, {{ \Carbon\Carbon::parse($tanggalTerbit)->translatedFormat('d F Y') }}<br>
                            {{ $jabatanPic }}
                            <div class="nama">{{ $namaPic }}</div>
                            <div class="nip">Human Resources General Affairs</div>
                            <div class="qr">
                                <img src="{{ $qrDataUri }}" alt="QR Verifikasi">
                                <div class="kode">{{ $kodeVerifikasi }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
