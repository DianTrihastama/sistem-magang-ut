<?php

namespace App\Mail;

use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PembimbingBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Penugasan Pembimbing Baru - Sistem Magang UT',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.pembimbing-baru');
    }
}
