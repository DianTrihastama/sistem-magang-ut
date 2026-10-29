<?php

namespace App\Mail;

use App\Models\Dokumen;
use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifikasiDokumenMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, Dokumen>  $dokumen
     */
    public function __construct(
        public Peserta $peserta,
        public array $dokumen = [],
        public bool $permintaanBaru = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->permintaanBaru
                ? 'Permintaan Melengkapi Dokumen Tahap 3 - Sistem Magang UT'
                : 'Hasil Verifikasi Dokumen Tahap 3 - Sistem Magang UT',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.verifikasi-dokumen');
    }
}
