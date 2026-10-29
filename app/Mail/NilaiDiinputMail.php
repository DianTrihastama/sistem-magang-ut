<?php

namespace App\Mail;

use App\Models\NilaiAkhir;
use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NilaiDiinputMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta,
        public NilaiAkhir $nilaiAkhir
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nilai Sudah Diinput untuk {$this->peserta->nama_lengkap}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nilai-diinput');
    }
}
