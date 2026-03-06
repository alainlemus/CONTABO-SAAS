<?php

namespace Tests\Feature\Commands;

use App\Enums\ObligationStatus;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateAnnualObligationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Client::flushEventListeners();
    }

    public function test_generates_annual_obligations_for_active_clients_with_tax_regime(): void
    {
        $admin = User::factory()->create();

        // Régimen 601 (PM): incluye DeclaracionAnualPm — vence 31 de marzo año siguiente
        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        // Debe existir la declaración anual PM para el año 2024
        $this->assertGreaterThan(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->count()
        );
    }

    public function test_generates_annual_obligations_for_pf_regime(): void
    {
        $admin = User::factory()->create();

        // Régimen 626 (RESICO PF): incluye DeclaracionAnualPf — vence 30 de abril
        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '626',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $this->assertGreaterThan(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->count()
        );
    }

    public function test_does_not_generate_obligations_for_inactive_clients(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'inactive',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $this->assertEquals(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count()
        );
    }

    public function test_does_not_generate_for_clients_without_tax_regime(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => null,
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $this->assertEquals(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count()
        );
    }

    public function test_does_not_create_duplicate_annual_obligations(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $countAfterFirst = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->count();

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $countAfterSecond = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->count();

        $this->assertEquals($countAfterFirst, $countAfterSecond);
    }

    public function test_uses_previous_year_as_default(): void
    {
        $admin = User::factory()->create();

        Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations')->assertSuccessful();

        $expectedYear = (int) now()->subYear()->format('Y');

        $this->assertGreaterThan(0, FiscalObligation::withoutGlobalScopes()
            ->where('period_year', $expectedYear)
            ->whereNull('period_month')
            ->count()
        );
    }

    public function test_outputs_client_and_obligation_counts(): void
    {
        $admin = User::factory()->create();

        Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])
            ->expectsOutputToContain('1')
            ->assertSuccessful();
    }

    public function test_annual_obligation_has_pending_status(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $obligation = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->first();

        $this->assertNotNull($obligation);
        $this->assertEquals(ObligationStatus::Pending, $obligation->status);
    }

    public function test_pm_annual_obligation_due_date_is_march_31(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601', // PM — vence 31 de marzo del año siguiente
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $obligation = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->first();

        $this->assertNotNull($obligation);
        $this->assertEquals('2025-03-31', $obligation->due_date->toDateString());
    }

    public function test_pf_annual_obligation_due_date_is_april_30(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '626', // PF RESICO — vence 30 de abril del año siguiente
        ]);

        $this->artisan('app:generate-annual-obligations', ['--year' => 2024])->assertSuccessful();

        $obligation = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2024)
            ->whereNull('period_month')
            ->first();

        $this->assertNotNull($obligation);
        $this->assertEquals('2025-04-30', $obligation->due_date->toDateString());
    }
}
