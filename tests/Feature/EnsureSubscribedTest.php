<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSubscribed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class EnsureSubscribedTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Ejecuta el middleware y devuelve la respuesta.
     * $next simplemente retorna un HTTP 200 vacío si el middleware lo permite pasar.
     */
    private function runMiddleware(User $user): \Symfony\Component\HttpFoundation\Response
    {
        $this->actingAs($user);

        $request = Request::create('/admin', 'GET');
        $request->setLaravelSession(session()->driver());

        $middleware = new EnsureSubscribed;

        return $middleware->handle($request, fn () => new Response('ok', 200));
    }

    // ─── Guest ────────────────────────────────────────────────────────────────

    public function test_unauthenticated_request_passes_through(): void
    {
        // Sin usuario autenticado, el middleware no bloquea (la autenticación
        // la maneja otro middleware anterior).
        $request = Request::create('/admin', 'GET');
        $request->setLaravelSession(session()->driver());

        $middleware = new EnsureSubscribed;
        $response = $middleware->handle($request, fn () => new Response('ok', 200));

        $this->assertEquals(200, $response->getStatusCode());
    }

    // ─── Admin — Trial activo ─────────────────────────────────────────────────

    public function test_admin_on_generic_trial_can_pass(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => now()->addDays(7),
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_admin_with_expired_trial_is_redirected(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => now()->subDay(),
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/subscription', $response->headers->get('Location'));
    }

    public function test_admin_with_no_trial_and_no_subscription_is_redirected(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/subscription', $response->headers->get('Location'));
    }

    // ─── Admin — Suscripción activa ───────────────────────────────────────────

    public function test_admin_with_active_subscription_can_pass(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        // Crear una suscripción activa directamente en BD (sin Stripe real)
        $admin->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => config('services.stripe.price_id', 'price_test'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_admin_with_canceled_subscription_in_grace_period_can_pass(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        // Suscripción cancelada pero en grace period (ends_at futuro)
        $admin->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => config('services.stripe.price_id', 'price_test'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => now()->addDays(5),
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_admin_with_expired_subscription_is_redirected(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        // Suscripción expirada (ends_at pasado)
        $admin->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'canceled',
            'stripe_price' => config('services.stripe.price_id', 'price_test'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => now()->subDay(),
        ]);

        $response = $this->runMiddleware($admin);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/subscription', $response->headers->get('Location'));
    }

    // ─── Capturista — hereda del owner ────────────────────────────────────────

    public function test_capturista_passes_when_owner_is_on_trial(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => now()->addDays(10),
        ]);

        $capturista = User::factory()->capturista()->create([
            'owner_id' => $admin->id,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($capturista);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_capturista_is_redirected_when_owner_has_no_access(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        $capturista = User::factory()->capturista()->create([
            'owner_id' => $admin->id,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($capturista);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/subscription', $response->headers->get('Location'));
    }

    public function test_capturista_passes_when_owner_has_active_subscription(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        $admin->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => config('services.stripe.price_id', 'price_test'),
            'quantity' => 1,
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);

        $capturista = User::factory()->capturista()->create([
            'owner_id' => $admin->id,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($capturista);

        $this->assertEquals(200, $response->getStatusCode());
    }

    // ─── Viewer — hereda del owner ────────────────────────────────────────────

    public function test_viewer_passes_when_owner_is_on_trial(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => now()->addDays(3),
        ]);

        $viewer = User::factory()->viewer()->create([
            'owner_id' => $admin->id,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($viewer);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_viewer_is_redirected_when_owner_has_no_access(): void
    {
        $admin = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        $viewer = User::factory()->viewer()->create([
            'owner_id' => $admin->id,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($viewer);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/subscription', $response->headers->get('Location'));
    }

    // ─── Capturista sin owner_id (edge case) ─────────────────────────────────

    public function test_capturista_without_owner_passes_through(): void
    {
        // Edge case: capturista huérfano (owner borrado) — no bloqueamos
        $capturista = User::factory()->capturista()->create([
            'owner_id' => null,
            'trial_ends_at' => null,
        ]);

        $response = $this->runMiddleware($capturista);

        // El middleware deja pasar si no encuentra owner (deja que otro middleware lo maneje)
        $this->assertEquals(200, $response->getStatusCode());
    }
}
