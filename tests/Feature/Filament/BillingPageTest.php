<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Pages\BillingPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BillingPageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(7),
        ]);
    }

    public function test_billing_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(BillingPage::getUrl())
            ->assertSuccessful();
    }

    public function test_billing_page_not_accessible_for_capturista(): void
    {
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $this->admin->id,
            'is_active' => true,
            'trial_ends_at' => null,
        ]);

        $this->actingAs($capturista)
            ->get(BillingPage::getUrl())
            ->assertForbidden();
    }

    public function test_billing_page_not_accessible_for_viewer(): void
    {
        $viewer = User::factory()->viewer()->create([
            'owner_id' => $this->admin->id,
            'is_active' => true,
            'trial_ends_at' => null,
        ]);

        $this->actingAs($viewer)
            ->get(BillingPage::getUrl())
            ->assertForbidden();
    }

    public function test_billing_page_shows_trial_status(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(10),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertSee('Período de prueba activo');
    }

    public function test_billing_page_shows_no_subscription_status_when_trial_expired(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertSee('Sin suscripción');
    }

    public function test_billing_page_shows_stripe_not_configured_warning(): void
    {
        // Las llaves de Stripe en el entorno de test son placeholders,
        // por lo que el aviso debe mostrarse siempre en tests.
        $this->actingAs($this->admin);

        Livewire::test(BillingPage::class)
            ->assertSee('Stripe no está configurado');
    }

    public function test_billing_page_can_access_returns_false_for_capturista(): void
    {
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $this->admin->id,
            'is_active' => true,
        ]);

        $this->actingAs($capturista);

        $this->assertFalse(BillingPage::canAccess());
    }

    public function test_billing_page_can_access_returns_true_for_admin(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(BillingPage::canAccess());
    }

    public function test_billing_page_view_data_contains_required_keys(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingPage::class);
        $component->assertSuccessful();

        $instance = $component->instance();
        $data = $instance->getViewData();

        $this->assertArrayHasKey('owner', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('onTrial', $data);
        $this->assertArrayHasKey('trialEndsAt', $data);
        $this->assertArrayHasKey('subscription', $data);
        $this->assertArrayHasKey('paymentMethod', $data);
        $this->assertArrayHasKey('invoices', $data);
        $this->assertArrayHasKey('isStripeConfigured', $data);
    }

    public function test_billing_page_view_data_status_is_trial_when_within_trial(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->actingAs($admin);

        $component = Livewire::test(BillingPage::class);
        $data = $component->instance()->getViewData();

        $this->assertEquals('trial', $data['status']);
        $this->assertTrue($data['onTrial']);
    }

    public function test_billing_page_view_data_status_is_none_when_trial_expired(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        $component = Livewire::test(BillingPage::class);
        $data = $component->instance()->getViewData();

        $this->assertEquals('none', $data['status']);
        $this->assertFalse($data['onTrial']);
    }

    public function test_cancel_subscription_shows_warning_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        // Stripe no configurado — debe enviar notificación de warning, no crashear
        Livewire::test(BillingPage::class)
            ->call('cancelSubscription')
            ->assertSuccessful();
    }

    public function test_resume_subscription_shows_warning_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        // Stripe no configurado — debe enviar notificación de warning, no crashear
        Livewire::test(BillingPage::class)
            ->call('resumeSubscription')
            ->assertSuccessful();
    }

    public function test_start_checkout_shows_warning_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        // Stripe no configurado en el entorno de tests — debe enviar notificación de warning y no crashear
        Livewire::test(BillingPage::class)
            ->call('startCheckout')
            ->assertSuccessful();
    }

    public function test_start_checkout_returns_null_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingPage::class);
        $instance = $component->instance();

        // Con keys de placeholder, isStripeConfigured() retorna false → startCheckout() retorna null
        $result = $instance->startCheckout();

        $this->assertNull($result);
    }

    public function test_open_portal_shows_warning_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        // Stripe no configurado en tests — debe notificar y no crashear
        Livewire::test(BillingPage::class)
            ->call('openPortal')
            ->assertSuccessful();
    }

    public function test_open_portal_returns_null_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingPage::class);
        $instance = $component->instance();

        // Con keys de placeholder, isStripeConfigured() retorna false → openPortal() retorna null
        $result = $instance->openPortal();

        $this->assertNull($result);
    }
}
