<?php

namespace Tests\Feature;

use App\Mail\PaymentMethodUpdatedMail;
use App\Mail\PaymentSucceededMail;
use App\Mail\SubscriptionActivatedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\TestCase;

class StripeWebhookMailTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function stripeUser(string $stripeId): User
    {
        return User::factory()->create([
            'stripe_id' => $stripeId,
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Despacha un WebhookReceived directamente, sin pasar por el WebhookController
     * de Cashier. Útil cuando el payload no tiene la estructura completa que Cashier
     * necesita para sus propios handlers.
     *
     * @param  array<string, mixed>  $payload
     */
    private function dispatchWebhook(array $payload): void
    {
        Event::dispatch(new WebhookReceived($payload));
    }

    // ─── HandleSubscriptionActivated ─────────────────────────────────────────

    public function test_subscription_activated_sends_mail_to_user(): void
    {
        Mail::fake();

        $user = $this->stripeUser('cus_activated1');

        $this->dispatchWebhook([
            'type' => 'customer.subscription.created',
            'data' => ['object' => ['customer' => 'cus_activated1']],
        ]);

        Mail::assertSent(SubscriptionActivatedMail::class, function (SubscriptionActivatedMail $mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });
    }

    public function test_subscription_activated_does_not_send_mail_for_unknown_customer(): void
    {
        Mail::fake();

        $this->dispatchWebhook([
            'type' => 'customer.subscription.created',
            'data' => ['object' => ['customer' => 'cus_nonexistent_mail']],
        ]);

        Mail::assertNotSent(SubscriptionActivatedMail::class);
    }

    public function test_subscription_activated_ignores_other_webhook_types(): void
    {
        Mail::fake();

        $this->stripeUser('cus_activated_ignore');

        $this->dispatchWebhook([
            'type' => 'customer.subscription.updated',
            'data' => ['object' => ['customer' => 'cus_activated_ignore']],
        ]);

        Mail::assertNotSent(SubscriptionActivatedMail::class);
    }

    // ─── HandlePaymentSucceeded ───────────────────────────────────────────────

    public function test_payment_succeeded_sends_mail_with_amount_and_date(): void
    {
        Mail::fake();

        $user = $this->stripeUser('cus_paysuccess1');
        $timestamp = now()->timestamp;

        $this->dispatchWebhook([
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_paysuccess1',
                    'amount_paid' => 29900,
                    'created' => $timestamp,
                ],
            ],
        ]);

        Mail::assertSent(PaymentSucceededMail::class, function (PaymentSucceededMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && $mail->user->is($user)
                && $mail->amountInCents === 29900;
        });
    }

    public function test_payment_succeeded_ignores_zero_amount_invoices(): void
    {
        Mail::fake();

        $this->stripeUser('cus_paysuccess_zero');

        $this->dispatchWebhook([
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_paysuccess_zero',
                    'amount_paid' => 0,
                    'created' => now()->timestamp,
                ],
            ],
        ]);

        Mail::assertNotSent(PaymentSucceededMail::class);
    }

    public function test_payment_succeeded_does_not_send_mail_for_unknown_customer(): void
    {
        Mail::fake();

        $this->dispatchWebhook([
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_nonexistent_pay',
                    'amount_paid' => 9900,
                    'created' => now()->timestamp,
                ],
            ],
        ]);

        Mail::assertNotSent(PaymentSucceededMail::class);
    }

    public function test_payment_succeeded_ignores_other_webhook_types(): void
    {
        Mail::fake();

        $this->stripeUser('cus_paysuccess_ignore');

        $this->dispatchWebhook([
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_paysuccess_ignore',
                    'amount_paid' => 9900,
                    'created' => now()->timestamp,
                ],
            ],
        ]);

        Mail::assertNotSent(PaymentSucceededMail::class);
    }

    // ─── HandlePaymentMethodUpdated ───────────────────────────────────────────

    public function test_payment_method_updated_sends_mail_when_invoice_settings_change(): void
    {
        Mail::fake();

        $user = $this->stripeUser('cus_pmupdate1');

        $this->dispatchWebhook([
            'type' => 'customer.updated',
            'data' => [
                'object' => [
                    'id' => 'cus_pmupdate1',
                    'invoice_settings' => [
                        'default_payment_method' => 'pm_new123',
                    ],
                ],
                'previous_attributes' => [
                    'invoice_settings' => [
                        'default_payment_method' => 'pm_old456',
                    ],
                ],
            ],
        ]);

        Mail::assertSent(PaymentMethodUpdatedMail::class, function (PaymentMethodUpdatedMail $mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });
    }

    public function test_payment_method_updated_ignores_unrelated_customer_updates(): void
    {
        Mail::fake();

        $this->stripeUser('cus_pmupdate_ignore');

        // customer.updated sin cambio en invoice_settings
        $this->dispatchWebhook([
            'type' => 'customer.updated',
            'data' => [
                'object' => [
                    'id' => 'cus_pmupdate_ignore',
                    'email' => 'new@example.com',
                ],
                'previous_attributes' => [
                    'email' => 'old@example.com',
                ],
            ],
        ]);

        Mail::assertNotSent(PaymentMethodUpdatedMail::class);
    }

    public function test_payment_method_updated_does_not_send_mail_for_unknown_customer(): void
    {
        Mail::fake();

        $this->dispatchWebhook([
            'type' => 'customer.updated',
            'data' => [
                'object' => [
                    'id' => 'cus_nonexistent_pm',
                    'invoice_settings' => ['default_payment_method' => 'pm_new'],
                ],
                'previous_attributes' => [
                    'invoice_settings' => ['default_payment_method' => 'pm_old'],
                ],
            ],
        ]);

        Mail::assertNotSent(PaymentMethodUpdatedMail::class);
    }

    public function test_payment_method_updated_ignores_other_webhook_types(): void
    {
        Mail::fake();

        $this->stripeUser('cus_pmupdate_type_ignore');

        $this->dispatchWebhook([
            'type' => 'customer.deleted',
            'data' => [
                'object' => ['id' => 'cus_pmupdate_type_ignore'],
                'previous_attributes' => [],
            ],
        ]);

        Mail::assertNotSent(PaymentMethodUpdatedMail::class);
    }
}
