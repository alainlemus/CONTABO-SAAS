<?php

namespace App\Listeners;

use App\Mail\PaymentMethodUpdatedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

class HandlePaymentMethodUpdated implements ShouldQueue
{
    public function __construct() {}

    /**
     * Solo procesa el evento `customer.updated` de Stripe cuando el
     * método de pago predeterminado cambia. Envía email de aviso de seguridad.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'customer.updated') {
            return;
        }

        $previousAttributes = $event->payload['data']['previous_attributes'] ?? [];

        // Solo actuar si el default_payment_method realmente cambió.
        if (! array_key_exists('invoice_settings', $previousAttributes)) {
            return;
        }

        $customerId = $event->payload['data']['object']['id'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        Mail::to($user->email)->send(new PaymentMethodUpdatedMail($user));
    }
}
