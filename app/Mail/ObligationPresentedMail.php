<?php

namespace App\Mail;

use App\Models\FiscalObligation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ObligationPresentedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FiscalObligation $obligation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Tu obligación fiscal fue presentada ante el SAT',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.obligation-presented',
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
