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
        $this->assertArrayHasKey('paymentMethods', $data);
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

    public function test_payment_method_section_hidden_when_on_trial(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertDontSee('Método de pago');
    }

    public function test_payment_history_section_hidden_when_on_trial(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertDontSee('Historial de pagos');
    }

    public function test_payment_method_section_hidden_when_no_subscription(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertDontSee('Método de pago');
    }

    public function test_payment_history_section_hidden_when_no_subscription(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        Livewire::test(BillingPage::class)
            ->assertDontSee('Historial de pagos');
    }

    // ─── Modal de confirmación de cancelación ─────────────────────────────────

    public function test_cancel_subscription_action_exists(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(BillingPage::class)
            ->assertActionExists('cancelSubscription');
    }

    public function test_cancel_subscription_action_modal_heading(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingPage::class);
        $instance = $component->instance();

        $action = $instance->cancelSubscriptionAction();

        $this->assertEquals('Cancelar suscripción', $action->getModalHeading());
    }

    public function test_cancel_subscription_action_modal_submit_label(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(BillingPage::class);
        $instance = $component->instance();

        $action = $instance->cancelSubscriptionAction();

        $this->assertEquals('Sí, cancelar suscripción', $action->getModalSubmitActionLabel());
    }

    public function test_cancel_subscription_action_requires_password(): void
    {
        $this->actingAs($this->admin);

        // Sin contraseña — debe fallar validación
        Livewire::test(BillingPage::class)
            ->callAction('cancelSubscription', data: [])
            ->assertHasActionErrors(['password' => 'required']);
    }

    public function test_cancel_subscription_action_rejects_wrong_password(): void
    {
        $this->actingAs($this->admin);

        // Con contraseña incorrecta, el action debe detenerse (halt) sin ejecutar cancelSubscription()
        Livewire::test(BillingPage::class)
            ->callAction('cancelSubscription', data: ['password' => 'wrong-password'])
            ->assertActionHalted('cancelSubscription');
    }

    public function test_cancel_subscription_action_with_correct_password_does_not_crash(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(7),
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);

        $this->actingAs($admin);

        // Stripe no configurado — con contraseña correcta ejecuta cancelSubscription()
        // que envía notificación de warning sin crashear
        Livewire::test(BillingPage::class)
            ->callAction('cancelSubscription', data: ['password' => 'secret123'])
            ->assertSuccessful();
    }
}
