<?php

namespace App\Mail;

use App\Models\Peserta;
use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SertifikatTerbitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta,
        public Sertifikat $sertifikat
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sertifikat Magang Sudah Terbit - Sistem Magang UT',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.sertifikat-terbit');
    }
}
