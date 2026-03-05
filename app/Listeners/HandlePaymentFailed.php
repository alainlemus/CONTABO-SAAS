<?php

namespace App\Listeners;

use App\Mail\PaymentFailedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

class HandlePaymentFailed implements ShouldQueue
{
    public function __construct() {}

    /**
     * Solo procesa el evento `invoice.payment_failed` de Stripe.
     * Envía un email al admin dueño de la suscripción.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'invoice.payment_failed') {
            return;
        }

        $customerId = $event->payload['data']['object']['customer'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        Mail::to($user->email)->send(new PaymentFailedMail($user));
    }
}
