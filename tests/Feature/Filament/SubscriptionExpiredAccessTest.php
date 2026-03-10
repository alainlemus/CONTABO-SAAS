<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Pages\BillingPage;
use App\Filament\Pages\SubscriptionExpiredPage;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionExpiredAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin cuya suscripción y trial ya expiraron completamente.
     * Tiene stripe_id para simular que alguna vez tuvo suscripción.
     */
    private function expiredAdmin(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->subDays(10),
            'stripe_id' => 'cus_expired_test',
        ]);
    }

    private function activeAdmin(): User
    {
        return User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(7),
        ]);
    }

    // ─── hasActiveAccess() ────────────────────────────────────────────────────

    public function test_active_admin_has_active_access(): void
    {
        $admin = $this->activeAdmin();

        $this->assertTrue($admin->hasActiveAccess());
    }

    public function test_expired_admin_has_no_active_access(): void
    {
        $admin = $this->expiredAdmin();

        $this->assertFalse($admin->hasActiveAccess());
    }

    public function test_team_member_inherits_active_access_from_owner(): void
    {
        $owner = $this->activeAdmin();
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $owner->id,
            'is_active' => true,
            'trial_ends_at' => null,
        ]);

        $this->assertTrue($capturista->hasActiveAccess());
    }

    public function test_team_member_inherits_expired_access_from_owner(): void
    {
        $owner = $this->expiredAdmin();
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $owner->id,
            'is_active' => true,
            'trial_ends_at' => null,
        ]);

        $this->assertFalse($capturista->hasActiveAccess());
    }

    // ─── isSubscriptionExpired() ──────────────────────────────────────────────

    public function test_expired_admin_is_subscription_expired(): void
    {
        $admin = $this->expiredAdmin();

        $this->assertTrue($admin->isSubscriptionExpired());
    }

    public function test_active_trial_admin_is_not_subscription_expired(): void
    {
        $admin = $this->activeAdmin();

        $this->assertFalse($admin->isSubscriptionExpired());
    }

    public function test_brand_new_user_without_trial_is_not_subscription_expired(): void
    {
        // Usuario sin trial y sin stripe_id → debe ir a subscription.index, no a la página expirada
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => null,
            'stripe_id' => null,
        ]);

        $this->assertFalse($admin->isSubscriptionExpired());
    }

    // ─── EnsureSubscribed middleware — redirección a subscription-expired ─────

    public function test_expired_admin_is_redirected_to_subscription_expired_page(): void
    {
        $admin = $this->expiredAdmin();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect('/admin/subscription-expired-page');
    }

    public function test_expired_admin_can_access_subscription_expired_page(): void
    {
        $admin = $this->expiredAdmin();

        $this->actingAs($admin)
            ->get('/admin/subscription-expired-page')
            ->assertSuccessful();
    }

    public function test_expired_admin_can_access_billing_page(): void
    {
        $admin = $this->expiredAdmin();

        $this->actingAs($admin)
            ->get(BillingPage::getUrl())
            ->assertSuccessful();
    }

    public function test_expired_team_member_is_redirected_to_subscription_expired_page(): void
    {
        $owner = $this->expiredAdmin();
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $owner->id,
            'is_active' => true,
            'trial_ends_at' => null,
        ]);

        $this->actingAs($capturista)
            ->get('/admin')
            ->assertRedirect('/admin/subscription-expired-page');
    }

    public function test_active_admin_is_not_redirected_to_subscription_expired(): void
    {
        $admin = $this->activeAdmin();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    }

    public function test_brand_new_user_is_redirected_to_subscription_index_not_expired(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => null,
            'stripe_id' => null,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertRedirect(route('subscription.index'));
    }

    // ─── SubscriptionExpiredPage::canAccess() ─────────────────────────────────

    public function test_subscription_expired_page_can_access_for_expired_admin(): void
    {
        $admin = $this->expiredAdmin();
        $this->actingAs($admin);

        $this->assertTrue(SubscriptionExpiredPage::canAccess());
    }

    public function test_subscription_expired_page_cannot_access_for_active_admin(): void
    {
        $admin = $this->activeAdmin();
        $this->actingAs($admin);

        $this->assertFalse(SubscriptionExpiredPage::canAccess());
    }

    // ─── Acciones deshabilitadas en recursos ──────────────────────────────────

    public function test_expired_admin_cannot_see_create_client_action(): void
    {
        $admin = $this->expiredAdmin();
        $this->actingAs($admin);

        Livewire::test(ListClients::class)
            ->assertActionHidden('create');
    }

    public function test_active_admin_can_see_create_client_action(): void
    {
        $admin = $this->activeAdmin();
        $this->actingAs($admin);

        Livewire::test(ListClients::class)
            ->assertActionExists('create');
    }

    public function test_expired_admin_cannot_see_create_invoice_action(): void
    {
        $admin = $this->expiredAdmin();
        $this->actingAs($admin);

        Livewire::test(ListInvoices::class)
            ->assertActionHidden('create');
    }

    public function test_expired_admin_cannot_see_create_fiscal_obligation_action(): void
    {
        $admin = $this->expiredAdmin();
        $this->actingAs($admin);

        Livewire::test(ListFiscalObligations::class)
            ->assertActionHidden('create');
    }
}
