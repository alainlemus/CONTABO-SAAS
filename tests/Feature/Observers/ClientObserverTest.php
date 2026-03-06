<?php

namespace Tests\Feature\Observers;

use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientObserverTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);
    }

    public function test_creating_client_with_tax_regime_generates_annual_obligations(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => '601',
        ]);

        $obligations = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->get();

        $this->assertGreaterThan(0, $obligations->count());
        $this->assertTrue(
            $obligations->every(fn ($o) => $o->period_year === now()->year)
        );
    }

    public function test_creating_client_without_tax_regime_does_not_generate_obligations(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => null,
        ]);

        $count = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count();

        $this->assertSame(0, $count);
    }

    public function test_updating_tax_regime_generates_new_obligations(): void
    {
        // Cliente sin régimen → sin obligaciones
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => null,
        ]);

        $this->assertSame(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count());

        // Asignar régimen fiscal → debe generar obligaciones
        $client->update(['tax_regime' => '612']);

        $this->assertGreaterThan(0, FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count());
    }

    public function test_updating_other_field_does_not_generate_obligations(): void
    {
        // Cliente sin régimen fiscal
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => null,
        ]);

        // Actualizar un campo que no es tax_regime
        $client->update(['name' => 'Nuevo Nombre SA de CV']);

        $count = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count();

        $this->assertSame(0, $count);
    }

    public function test_updating_tax_regime_does_not_duplicate_existing_obligations(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => '601',
        ]);

        $countAfterCreate = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count();

        $this->assertGreaterThan(0, $countAfterCreate);

        // Actualizar al mismo régimen → no debe duplicar
        $client->update(['tax_regime' => '601']);

        $countAfterUpdate = FiscalObligation::withoutGlobalScopes()
            ->where('client_id', $client->id)
            ->count();

        $this->assertSame($countAfterCreate, $countAfterUpdate);
    }
}
