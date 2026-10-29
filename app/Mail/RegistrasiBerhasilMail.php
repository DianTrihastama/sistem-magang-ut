<?php

namespace App\Mail;

use App\Models\Peserta;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrasiBerhasilMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?Peserta $peserta
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registrasi Berhasil - Sistem Magang PT United Tractors Samarinda',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.registrasi-berhasil');
    }
}
