<?php

namespace App\Mail;

use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ObligationDueSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param  \Illuminate\Support\Collection<int, FiscalObligation>  $obligations */
    public function __construct(
        public User $user,
        public \Illuminate\Support\Collection $obligations,
        public int $daysUntilDue,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ Tienes {$this->obligations->count()} obligación(es) fiscal(es) por vencer en {$this->daysUntilDue} días",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.obligation-due-soon',
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
