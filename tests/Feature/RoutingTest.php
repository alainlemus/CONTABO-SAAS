<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutingTest extends TestCase
{
    public function test_landing_page_returns_200(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('landing');
    }

    public function test_privacy_page_returns_200(): void
    {
        $response = $this->get('/aviso-de-privacidad');

        $response->assertStatus(200);
        $response->assertViewIs('legal.privacy');
    }

    public function test_terms_page_returns_200(): void
    {
        $response = $this->get('/terminos-y-condiciones');

        $response->assertStatus(200);
        $response->assertViewIs('legal.terms');
    }

    public function test_admin_register_route_does_not_exist(): void
    {
        $response = $this->get('/admin/register');

        $response->assertStatus(404);
    }

    public function test_stripe_webhook_route_is_registered(): void
    {
        // Cashier registra POST /stripe/webhook automáticamente.
        // En el entorno de testing STRIPE_WEBHOOK_SECRET está vacío, por lo que
        // el middleware de firma se omite y el endpoint devuelve 200.
        // Lo importante es que la ruta existe (no devuelve 404 ni 405).
        $this->postJson('/stripe/webhook', [
            'type' => 'unknown.event',
            'data' => ['object' => []],
        ])->assertSuccessful();
    }
}
