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

    public function test_admin_register_route_does_not_exist(): void
    {
        $response = $this->get('/admin/register');

        $response->assertStatus(404);
    }

    public function test_stripe_webhook_route_is_registered(): void
    {
        // Cashier registra POST /stripe/webhook automáticamente.
        // Sin firma válida devuelve 400 o 403 (no 404 — la ruta sí existe).
        $response = $this->postJson('/stripe/webhook', []);

        $this->assertContains($response->status(), [400, 403]);
    }
}
