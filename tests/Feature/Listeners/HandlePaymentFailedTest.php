<?php

namespace Tests\Feature\Listeners;

use App\Listeners\HandlePaymentFailed;
use App\Mail\PaymentFailedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\TestCase;

class HandlePaymentFailedTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(array $payload): WebhookReceived
    {
        return new WebhookReceived($payload);
    }

    public function test_sends_payment_failed_mail_when_invoice_payment_failed_event_received(): void
    {
        Mail::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_test123']);

        $event = $this->makeEvent([
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_test123',
                ],
            ],
        ]);

        (new HandlePaymentFailed)->handle($event);

        Mail::assertSent(PaymentFailedMail::class, function (PaymentFailedMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_does_not_send_mail_for_other_event_types(): void
    {
        Mail::fake();

        User::factory()->create(['stripe_id' => 'cus_test456']);

        $event = $this->makeEvent([
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'customer' => 'cus_test456',
                ],
            ],
        ]);

        (new HandlePaymentFailed)->handle($event);

        Mail::assertNothingSent();
    }

    public function test_does_not_send_mail_when_no_matching_user_for_stripe_id(): void
    {
        Mail::fake();

        $event = $this->makeEvent([
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'customer' => 'cus_nonexistent',
                ],
            ],
        ]);

        (new HandlePaymentFailed)->handle($event);

        Mail::assertNothingSent();
    }

    public function test_does_not_send_mail_when_customer_id_is_missing(): void
    {
        Mail::fake();

        $event = $this->makeEvent([
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [],
            ],
        ]);

        (new HandlePaymentFailed)->handle($event);

        Mail::assertNothingSent();
    }

    public function test_payment_failed_mail_has_correct_subject(): void
    {
        $user = User::factory()->create();
        $mail = new PaymentFailedMail($user);

        $this->assertStringContainsString('Problema con tu pago', $mail->envelope()->subject);
    }

    public function test_payment_failed_mail_uses_correct_view(): void
    {
        $user = User::factory()->create();
        $mail = new PaymentFailedMail($user);

        $this->assertEquals('emails.payment-failed', $mail->content()->view);
    }
}
