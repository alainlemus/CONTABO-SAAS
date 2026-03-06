<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Envía un payload de webhook.
     * En tests, STRIPE_WEBHOOK_SECRET no está configurado, por lo que el middleware
     * de firma se omite automáticamente (ver WebhookController::__construct).
     *
     * @param  array<string, mixed>  $payload
     */
    private function postWebhook(array $payload): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/stripe/webhook', $payload);
    }

    /**
     * Crea un usuario con stripe_id y sin trial ni suscripción activa.
     */
    private function stripeUser(string $stripeId): User
    {
        return User::factory()->create([
            'stripe_id' => $stripeId,
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Crea una suscripción en BD para el usuario sin llamar a Stripe.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function createSubscription(User $user, array $overrides = []): \Laravel\Cashier\Subscription
    {
        return $user->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => 'price_test',
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ], $overrides));
    }

    // ─── Ruta y eventos generales ─────────────────────────────────────────────

    public function test_webhook_endpoint_exists_and_returns_200(): void
    {
        $this->postWebhook([
            'type' => 'unknown.event',
            'data' => ['object' => []],
        ])->assertStatus(200);
    }

    public function test_webhook_rejects_get_requests(): void
    {
        $this->get('/stripe/webhook')->assertStatus(405);
    }

    public function test_webhook_dispatches_webhook_received_event(): void
    {
        Event::fake([WebhookReceived::class]);

        $this->postWebhook([
            'type' => 'unknown.event',
            'data' => ['object' => []],
        ]);

        Event::assertDispatched(WebhookReceived::class);
    }

    // ─── customer.subscription.created ───────────────────────────────────────

    public function test_subscription_created_creates_subscription_in_database(): void
    {
        $user = $this->stripeUser('cus_created1');

        $this->postWebhook([
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_newtest1',
                    'customer' => 'cus_created1',
                    'status' => 'active',
                    'trial_end' => null,
                    'cancel_at_period_end' => false,
                    'cancel_at' => null,
                    'canceled_at' => null,
                    'metadata' => ['type' => 'default'],
                    'items' => [
                        'data' => [[
                            'id' => 'si_test1',
                            'price' => ['id' => 'price_test', 'product' => 'prod_test'],
                            'quantity' => 1,
                        ]],
                    ],
                ],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'stripe_id' => 'sub_newtest1',
            'stripe_status' => 'active',
        ]);
    }

    public function test_subscription_created_clears_generic_trial(): void
    {
        // Usuario con trial activo y SIN suscripción previa en BD.
        // El webhook debe crear la suscripción Y borrar trial_ends_at.
        $user = User::factory()->create([
            'stripe_id' => 'cus_cleartrial',
            'trial_ends_at' => now()->addDays(10),
        ]);

        $this->postWebhook([
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_cleartrial1',
                    'customer' => 'cus_cleartrial',
                    'status' => 'active',
                    'trial_end' => null,
                    'cancel_at_period_end' => false,
                    'cancel_at' => null,
                    'canceled_at' => null,
                    'metadata' => [],
                    'items' => [
                        'data' => [[
                            'id' => 'si_cleartrial1',
                            'price' => ['id' => 'price_test', 'product' => 'prod_test'],
                            'quantity' => 1,
                        ]],
                    ],
                ],
            ],
        ])->assertStatus(200);

        $this->assertNull($user->fresh()->trial_ends_at);
    }

    public function test_subscription_created_for_unknown_customer_returns_200(): void
    {
        $this->postWebhook([
            'type' => 'customer.subscription.created',
            'data' => [
                'object' => [
                    'id' => 'sub_unknown99',
                    'customer' => 'cus_nonexistent',
                    'status' => 'active',
                    'trial_end' => null,
                    'cancel_at_period_end' => false,
                    'cancel_at' => null,
                    'canceled_at' => null,
                    'metadata' => [],
                    'items' => ['data' => []],
                ],
            ],
        ])->assertStatus(200);
    }

    // ─── customer.subscription.updated ───────────────────────────────────────

    public function test_subscription_updated_marks_ends_at_when_cancel_at_period_end(): void
    {
        $user = $this->stripeUser('cus_updated1');
        $subscription = $this->createSubscription($user, ['stripe_id' => 'sub_updated1']);

        // Usamos cancel_at (timestamp futuro) para evitar que Cashier llame
        // a Stripe API para obtener currentPeriodEnd().
        $cancelAt = now()->addDays(30)->timestamp;

        $this->postWebhook([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_updated1',
                    'customer' => 'cus_updated1',
                    'status' => 'active',
                    'trial_end' => null,
                    'cancel_at_period_end' => false,
                    'current_period_end' => $cancelAt,
                    'cancel_at' => $cancelAt,
                    'canceled_at' => null,
                    'metadata' => [],
                    'items' => [
                        'data' => [[
                            'id' => 'si_updated1',
                            'price' => ['id' => 'price_test', 'product' => 'prod_test'],
                            'quantity' => 1,
                        ]],
                    ],
                ],
            ],
        ])->assertStatus(200);

        $this->assertNotNull($subscription->fresh()->ends_at);
    }

    public function test_subscription_updated_changes_stripe_status(): void
    {
        $user = $this->stripeUser('cus_updated2');
        $this->createSubscription($user, [
            'stripe_id' => 'sub_updated2',
            'stripe_status' => 'active',
        ]);

        $this->postWebhook([
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_updated2',
                    'customer' => 'cus_updated2',
                    'status' => 'past_due',
                    'trial_end' => null,
                    'cancel_at_period_end' => false,
                    'cancel_at' => null,
                    'canceled_at' => null,
                    'metadata' => [],
                    'items' => [
                        'data' => [[
                            'id' => 'si_updated2',
                            'price' => ['id' => 'price_test', 'product' => 'prod_test'],
                            'quantity' => 1,
                        ]],
                    ],
                ],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'stripe_id' => 'sub_updated2',
            'stripe_status' => 'past_due',
        ]);
    }

    // ─── customer.subscription.deleted ───────────────────────────────────────

    public function test_subscription_deleted_marks_subscription_as_canceled(): void
    {
        $user = $this->stripeUser('cus_deleted1');
        $this->createSubscription($user, ['stripe_id' => 'sub_deleted1']);

        $this->postWebhook([
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_deleted1',
                    'customer' => 'cus_deleted1',
                    'status' => 'canceled',
                    'metadata' => [],
                    'items' => ['data' => []],
                ],
            ],
        ])->assertStatus(200);

        $this->assertDatabaseHas('subscriptions', [
            'stripe_id' => 'sub_deleted1',
            'stripe_status' => 'canceled',
        ]);
    }

    public function test_subscription_deleted_for_unknown_customer_returns_200(): void
    {
        $this->postWebhook([
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'id' => 'sub_unknown99',
                    'customer' => 'cus_nonexistent',
                    'status' => 'canceled',
                    'metadata' => [],
                    'items' => ['data' => []],
                ],
            ],
        ])->assertStatus(200);
    }

    // ─── customer.deleted ─────────────────────────────────────────────────────

    public function test_customer_deleted_clears_stripe_id_from_user(): void
    {
        $user = $this->stripeUser('cus_custdel1');
        $this->createSubscription($user);

        $this->postWebhook([
            'type' => 'customer.deleted',
            'data' => [
                'object' => ['id' => 'cus_custdel1'],
            ],
        ])->assertStatus(200);

        $this->assertNull($user->fresh()->stripe_id);
    }

    public function test_customer_deleted_cancels_all_subscriptions(): void
    {
        $user = $this->stripeUser('cus_custdel2');
        $this->createSubscription($user, ['stripe_id' => 'sub_custdel2a']);
        $this->createSubscription($user, ['stripe_id' => 'sub_custdel2b']);

        $this->postWebhook([
            'type' => 'customer.deleted',
            'data' => [
                'object' => ['id' => 'cus_custdel2'],
            ],
        ])->assertStatus(200);

        $user->refresh();
        $this->assertEmpty($user->subscriptions()->where('stripe_status', 'active')->get());
    }
}
