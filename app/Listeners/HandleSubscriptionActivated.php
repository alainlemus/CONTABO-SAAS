<?php

namespace App\Listeners;

use App\Mail\SubscriptionActivatedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

class HandleSubscriptionActivated implements ShouldQueue
{
    public function __construct() {}

    /**
     * Solo procesa el evento `customer.subscription.created` de Stripe.
     * Envía un email de bienvenida al admin dueño de la suscripción.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'customer.subscription.created') {
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

        Mail::to($user->email)->send(new SubscriptionActivatedMail($user));
    }
}
