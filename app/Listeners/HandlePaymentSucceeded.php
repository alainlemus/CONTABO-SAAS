<?php

namespace App\Listeners;

use App\Mail\PaymentSucceededMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;

class HandlePaymentSucceeded implements ShouldQueue
{
    public function __construct() {}

    /**
     * Solo procesa el evento `invoice.payment_succeeded` de Stripe.
     * Ignora facturas de $0 (setup/trial). Envía email de confirmación de pago.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] !== 'invoice.payment_succeeded') {
            return;
        }

        $invoice = $event->payload['data']['object'] ?? [];
        $customerId = $invoice['customer'] ?? null;
        $amountPaid = $invoice['amount_paid'] ?? 0;

        if (! $customerId || $amountPaid === 0) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $invoiceDate = Carbon::createFromTimestamp($invoice['created'] ?? now()->timestamp)
            ->format('d/m/Y');

        Mail::to($user->email)->send(new PaymentSucceededMail($user, $amountPaid, $invoiceDate));
    }
}
