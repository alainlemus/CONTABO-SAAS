<?php

namespace Tests\Unit\Services;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Services\FiscalObligationGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiscalObligationGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private FiscalObligationGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new FiscalObligationGenerator;

        // Los tests de este servicio crean clientes directamente para probar la lógica
        // del generador en aislamiento. El observer se prueba por separado en ClientObserverTest.
        Client::flushEventListeners();
    }

    // ─── generateForClient ────────────────────────────────────────────────────

    public function test_generates_obligations_for_regime_601(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        $obligations = $this->generator->generateForClient($client, 2025, 1);

        // 601: ISR mensual, IVA mensual, DIOT (mensuales) + Declaración anual PM (se genera tb aquí)
        $this->assertCount(4, $obligations);

        $types = $obligations->pluck('type')->map(fn ($t) => $t->value)->sort()->values()->all();
        $this->assertContains(ObligationType::IsrMensual->value, $types);
        $this->assertContains(ObligationType::IvaMensual->value, $types);
        $this->assertContains(ObligationType::Diot->value, $types);
        $this->assertContains(ObligationType::DeclaracionAnualPm->value, $types);
    }

    public function test_generates_obligations_for_regime_606(): void
    {
        $client = Client::factory()->create(['tax_regime' => '606']);

        $obligations = $this->generator->generateForClient($client, 2025, 3);

        // 606: ISR mensual, IVA mensual + Declaración anual PF (se genera también aquí)
        $this->assertCount(3, $obligations);

        $types = $obligations->pluck('type')->map(fn ($t) => $t->value)->all();
        $this->assertContains(ObligationType::IsrMensual->value, $types);
        $this->assertContains(ObligationType::IvaMensual->value, $types);
        $this->assertContains(ObligationType::DeclaracionAnualPf->value, $types);
    }

    public function test_generates_bimestral_obligations_for_regime_621_on_odd_months(): void
    {
        $client = Client::factory()->create(['tax_regime' => '621']);

        // Mes impar → genera bimestrales + anual PF
        $obligations = $this->generator->generateForClient($client, 2025, 1);

        $this->assertCount(3, $obligations);
        $types = $obligations->pluck('type')->map(fn ($t) => $t->value)->all();
        $this->assertContains(ObligationType::IsrBimestral->value, $types);
        $this->assertContains(ObligationType::IvaBimestral->value, $types);
        $this->assertContains(ObligationType::DeclaracionAnualPf->value, $types);
    }

    public function test_does_not_generate_bimestral_on_even_months(): void
    {
        $client = Client::factory()->create(['tax_regime' => '621']);

        // Mes par → NO genera bimestrales; la anual ya fue generada en mes anterior
        // Primero generamos el mes 1 para que la anual exista
        $this->generator->generateForClient($client, 2025, 1);

        // Mes 2: no genera bimestrales, y la anual ya existe (no se duplica)
        $obligations = $this->generator->generateForClient($client, 2025, 2);

        $this->assertCount(0, $obligations);
    }

    public function test_even_month_generates_only_annual_if_not_yet_created(): void
    {
        $client = Client::factory()->create(['tax_regime' => '621']);

        // Si el mes par es el primero que se llama, la anual aún no existe → se genera
        $obligations = $this->generator->generateForClient($client, 2025, 2);

        $this->assertCount(1, $obligations);
        $this->assertEquals(ObligationType::DeclaracionAnualPf->value, $obligations->first()->type->value);
    }

    public function test_returns_empty_collection_when_client_has_no_tax_regime(): void
    {
        $client = Client::factory()->create(['tax_regime' => null]);

        $obligations = $this->generator->generateForClient($client, 2025, 1);

        $this->assertCount(0, $obligations);
    }

    public function test_does_not_duplicate_existing_obligations(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        // Primera generación
        $first = $this->generator->generateForClient($client, 2025, 1);
        // Segunda generación del mismo mes — no debe duplicar
        $second = $this->generator->generateForClient($client, 2025, 1);

        $this->assertCount(4, $first); // 3 mensuales + 1 anual
        $this->assertCount(0, $second);
        $this->assertDatabaseCount('fiscal_obligations', 4);
    }

    public function test_monthly_obligation_due_date_is_17th_of_next_month(): void
    {
        $client = Client::factory()->create(['tax_regime' => '606']);

        $this->generator->generateForClient($client, 2025, 1);

        $obligation = FiscalObligation::where('type', ObligationType::IsrMensual->value)->first();
        $this->assertNotNull($obligation);
        $this->assertEquals('2025-02-17', $obligation->due_date->toDateString());
    }

    public function test_bimestral_obligation_due_date_is_17th_of_month_after_second(): void
    {
        $client = Client::factory()->create(['tax_regime' => '621']);

        // Bimestre Ene-Feb → vence 17 de marzo
        $this->generator->generateForClient($client, 2025, 1);

        $obligation = FiscalObligation::where('type', ObligationType::IsrBimestral->value)->first();
        $this->assertNotNull($obligation);
        $this->assertEquals('2025-03-17', $obligation->due_date->toDateString());
    }

    public function test_annual_pf_obligation_due_date_is_april_30(): void
    {
        $client = Client::factory()->create(['tax_regime' => '606']);

        $this->generator->generateYearForClient($client, 2025);

        $obligation = FiscalObligation::where('type', ObligationType::DeclaracionAnualPf->value)->first();
        $this->assertNotNull($obligation);
        $this->assertEquals('2026-04-30', $obligation->due_date->toDateString());
    }

    public function test_annual_pm_obligation_due_date_is_march_31(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        $this->generator->generateYearForClient($client, 2025);

        $obligation = FiscalObligation::where('type', ObligationType::DeclaracionAnualPm->value)->first();
        $this->assertNotNull($obligation);
        $this->assertEquals('2026-03-31', $obligation->due_date->toDateString());
    }

    // ─── generateYearForClient ────────────────────────────────────────────────

    public function test_generate_year_creates_12_months_for_monthly_regime(): void
    {
        $client = Client::factory()->create(['tax_regime' => '606']); // ISR + IVA mensual + anual PF

        $this->generator->generateYearForClient($client, 2025);

        // 12 meses × 2 tipos mensuales + 1 anual = 25 obligaciones
        $this->assertDatabaseCount('fiscal_obligations', 25);
    }

    public function test_generate_year_creates_6_bimesters_for_rif(): void
    {
        $client = Client::factory()->create(['tax_regime' => '621']); // ISR + IVA bimestral + anual PF

        $this->generator->generateYearForClient($client, 2025);

        // 6 bimestres × 2 tipos + 1 anual = 13 obligaciones
        $this->assertDatabaseCount('fiscal_obligations', 13);
    }

    public function test_generate_year_does_not_duplicate_annual(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        // Llamar dos veces no debe duplicar la anual
        $this->generator->generateYearForClient($client, 2025);
        $this->generator->generateYearForClient($client, 2025);

        $this->assertDatabaseCount(
            'fiscal_obligations',
            FiscalObligation::where('client_id', $client->id)->count()
        );
    }

    // ─── markOverdue ─────────────────────────────────────────────────────────

    public function test_mark_overdue_updates_past_due_pending_obligations(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        // Crear obligación pendiente con fecha ya pasada
        FiscalObligation::factory()->pending()->create([
            'client_id' => $client->id,
            'due_date' => Carbon::today()->subDay(),
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        // Crear obligación pendiente que NO ha vencido
        FiscalObligation::factory()->pending()->create([
            'client_id' => $client->id,
            'due_date' => Carbon::today()->addDay(),
            'type' => ObligationType::IvaMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $count = $this->generator->markOverdue();

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('fiscal_obligations', [
            'type' => ObligationType::IsrMensual->value,
            'status' => ObligationStatus::Overdue->value,
        ]);
        $this->assertDatabaseHas('fiscal_obligations', [
            'type' => ObligationType::IvaMensual->value,
            'status' => ObligationStatus::Pending->value,
        ]);
    }

    public function test_mark_overdue_does_not_affect_presented_obligations(): void
    {
        $client = Client::factory()->create(['tax_regime' => '601']);

        FiscalObligation::factory()->presented()->create([
            'client_id' => $client->id,
            'due_date' => Carbon::today()->subDay(),
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $count = $this->generator->markOverdue();

        $this->assertEquals(0, $count);
    }
}
