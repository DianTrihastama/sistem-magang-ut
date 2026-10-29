<?php

namespace App\Mail;

use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HasilSeleksiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta,
        public string $keputusan,
        public ?string $catatan
    ) {}

    public function envelope(): Envelope
    {
        $label = match ($this->keputusan) {
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            default => 'Perlu Revisi Berkas',
        };

        return new Envelope(
            subject: "Hasil Seleksi Administrasi: {$label} - Sistem Magang UT",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.hasil-seleksi');
    }
}
