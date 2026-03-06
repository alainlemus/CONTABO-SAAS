<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSucceededMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $amountInCents,
        public string $invoiceDate,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Pago recibido — CONTABO',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-succeeded',
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
