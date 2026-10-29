<?php

namespace App\Http\Requests;

use App\Services\DokumenService;
use Illuminate\Foundation\Http\FormRequest;

class PendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'user';
    }

    public function rules(): array
    {
        $peserta = $this->user()->peserta;
        $sudahAdaPeriode = $peserta?->tanggal_mulai !== null;

        $tanggalMulai = ['required', 'date'];

        // Aturan "tidak boleh sebelum hari ini" hanya berlaku saat periode belum pernah diisi.
        // Setelah tersimpan, peserta tetap dapat memperbarui data / mengunggah ulang berkas.
        if (! $sudahAdaPeriode) {
            $tanggalMulai[] = 'after_or_equal:today';
        }

        $rules = [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'digits:16'],
            'instansi_pendidikan' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'in:SMK,D3,S1,lainnya'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'divisi_diajukan' => ['required', 'string', 'max:255'],
        ];

        // Berkas Tahap 1: wajib bila belum pernah diunggah, opsional bila ingin mengganti.
        foreach (DokumenService::JENIS_TAHAP_1 as $jenis) {
            $sudahDiunggah = $peserta
                && $peserta->dokumen()->where('jenis', $jenis)->exists();

            $rules[$jenis] = [
                $sudahDiunggah ? 'nullable' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'surat_pengantar.required' => 'Surat pengantar dari instansi wajib diunggah.',
            'cv.required' => 'CV wajib diunggah.',
            'kartu_identitas.required' => 'Kartu identitas wajib diunggah.',
            'transkrip_nilai.required' => 'Transkrip nilai wajib diunggah.',
            'surat_pengantar.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'cv.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'kartu_identitas.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'transkrip_nilai.mimes' => 'Berkas harus berformat PDF, JPG, atau PNG.',
            'surat_pengantar.max' => 'Ukuran berkas maksimal 2 MB.',
            'cv.max' => 'Ukuran berkas maksimal 2 MB.',
            'kartu_identitas.max' => 'Ukuran berkas maksimal 2 MB.',
            'transkrip_nilai.max' => 'Ukuran berkas maksimal 2 MB.',
        ];
    }
}
