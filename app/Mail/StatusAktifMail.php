<?php

namespace App\Mail;

use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusAktifMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta,
        public string $pathSurat
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Aktif Magang & Surat Balasan - Sistem Magang UT',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.status-aktif');
    }

    public function attachments(): array
    {
        return [[
            'path' => storage_path('app/private/' . $this->pathSurat),
            'as' => 'Surat-Balasan-Magang.pdf',
            'mime' => 'application/pdf',
        ]];
    }
}
