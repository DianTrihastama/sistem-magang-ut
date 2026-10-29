<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        public ?int $pesertaId = null,
        public string $bulan = ''
    ) {}

    public function query()
    {
        $query = Absensi::query()->with('peserta')->orderByDesc('tanggal');

        if ($this->pesertaId) {
            $query->where('peserta_id', $this->pesertaId);
        }

        if ($this->bulan !== '') {
            $tanggal = \Carbon\Carbon::parse($this->bulan . '-01');
            $query->whereBetween('tanggal', [$tanggal->copy()->startOfMonth(), $tanggal->copy()->endOfMonth()]);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Nama Peserta', 'Jam Masuk', 'Status', 'Keterangan', 'Disetujui'];
    }

    public function map($row): array
    {
        return [
            $row->tanggal->format('d-m-Y'),
            $row->peserta->nama_lengkap ?? '-',
            $row->jam_masuk ?? '-',
            strtoupper($row->status),
            $row->keterangan ?? '-',
            $row->disetujui === null ? 'Menunggu' : ($row->disetujui ? 'Ya' : 'Tidak'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
