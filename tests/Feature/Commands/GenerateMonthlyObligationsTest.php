<?php

namespace Tests\Feature\Commands;

use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateMonthlyObligationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_obligations_for_active_clients_with_tax_regime(): void
    {
        $admin = User::factory()->create();

        // Régimen 601 (ISR Mensual + IVA Mensual + DIOT) = 3 obligaciones mensuales
        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '601',
        ]);

        $this->artisan('app:generate-monthly-obligations', [
            '--year' => 2025,
            '--month' => 1,
        ])->assertSuccessful();

        $this->assertEquals(3, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2025)
            ->where('period_month', 1)
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

        $this->artisan('app:generate-monthly-obligations', [
            '--year' => 2025,
            '--month' => 1,
        ])->assertSuccessful();

        $this->assertEquals(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count()
        );
    }

    public function test_does_not_generate_obligations_for_clients_without_tax_regime(): void
    {
        $admin = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => null,
        ]);

        $this->artisan('app:generate-monthly-obligations', [
            '--year' => 2025,
            '--month' => 1,
        ])->assertSuccessful();

        $this->assertEquals(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count()
        );
    }

    public function test_does_not_create_duplicate_obligations(): void
    {
        $admin = User::factory()->create();

        // Régimen 626 (RESICO): ISR Mensual + IVA Mensual = 2 obligaciones
        $client = Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '626',
        ]);

        $options = ['--year' => 2025, '--month' => 3];

        $this->artisan('app:generate-monthly-obligations', $options)->assertSuccessful();
        $this->artisan('app:generate-monthly-obligations', $options)->assertSuccessful();

        $this->assertEquals(2, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->where('period_year', 2025)
            ->where('period_month', 3)
            ->count()
        );
    }

    public function test_outputs_client_and_obligation_counts(): void
    {
        $admin = User::factory()->create();

        Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '626',
        ]);

        $this->artisan('app:generate-monthly-obligations', [
            '--year' => 2025,
            '--month' => 5,
        ])
            ->expectsOutputToContain('1')
            ->assertSuccessful();
    }

    public function test_uses_previous_month_as_default_period(): void
    {
        $admin = User::factory()->create();

        Client::factory()->create([
            'user_id' => $admin->id,
            'status' => 'active',
            'tax_regime' => '626',
        ]);

        $this->artisan('app:generate-monthly-obligations')->assertSuccessful();

        $expectedYear = (int) now()->subMonth()->format('Y');
        $expectedMonth = (int) now()->subMonth()->format('n');

        $this->assertGreaterThan(0, FiscalObligation::withoutGlobalScopes()
            ->where('period_year', $expectedYear)
            ->where('period_month', $expectedMonth)
            ->count()
        );
    }
}
