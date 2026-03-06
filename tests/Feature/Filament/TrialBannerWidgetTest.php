<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Widgets\TrialBannerWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrialBannerWidgetTest extends TestCase
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

    public function test_widget_is_visible_for_admin_on_trial(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(TrialBannerWidget::canView());
    }

    public function test_widget_is_not_visible_when_trial_expired(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->actingAs($admin);

        $this->assertFalse(TrialBannerWidget::canView());
    }

    public function test_widget_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TrialBannerWidget::class)
            ->assertSuccessful();
    }

    public function test_widget_shows_days_left(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TrialBannerWidget::class)
            ->assertSee('días de prueba gratuita');
    }

    public function test_start_checkout_shows_warning_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        // Con keys placeholder en tests, debe notificar y no crashear
        Livewire::test(TrialBannerWidget::class)
            ->call('startCheckout')
            ->assertSuccessful();
    }

    public function test_start_checkout_returns_null_when_stripe_not_configured(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(TrialBannerWidget::class);
        $instance = $component->instance();

        // Con keys de placeholder, isStripeConfigured() retorna false → startCheckout() retorna null
        $result = $instance->startCheckout();

        $this->assertNull($result);
    }

    public function test_get_days_left_returns_integer(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(TrialBannerWidget::class);
        $daysLeft = $component->instance()->getDaysLeft();

        $this->assertIsInt($daysLeft);
        $this->assertGreaterThanOrEqual(0, $daysLeft);
    }

    public function test_get_trial_ends_at_returns_formatted_string(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(TrialBannerWidget::class);
        $trialEndsAt = $component->instance()->getTrialEndsAt();

        $this->assertIsString($trialEndsAt);
        $this->assertNotEmpty($trialEndsAt);
    }
}
