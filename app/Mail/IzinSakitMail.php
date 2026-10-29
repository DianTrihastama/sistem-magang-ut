<?php

namespace App\Mail;

use App\Models\Absensi;
use App\Models\Peserta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IzinSakitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peserta $peserta,
        public Absensi $absensi,
        public bool $disetujui
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->disetujui ? 'Disetujui' : 'Ditolak';

        return new Envelope(
            subject: "Pengajuan {$this->absensi->status} {$status} - Sistem Magang UT",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.izin-sakit');
    }
}
