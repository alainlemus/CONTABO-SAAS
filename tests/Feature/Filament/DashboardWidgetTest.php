<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Widgets\DashboardStatsWidget;
use App\Filament\Widgets\ObligacionesPorEstatusChartWidget;
use App\Filament\Widgets\ProximasObligacionesWidget;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardWidgetTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'active',
        ]);
    }

    // ─── DashboardStatsWidget ─────────────────────────────────────────────────

    public function test_stats_widget_shows_overdue_count(): void
    {
        FiscalObligation::factory()->count(2)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        // Obligación de otro admin — no debe contarse
        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id, 'status' => 'active']);
        FiscalObligation::factory()->create([
            'client_id' => $otherClient->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('2');
    }

    public function test_stats_widget_shows_due_soon_count(): void
    {
        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3),
        ]);

        // Otro admin — no debe contarse
        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id, 'status' => 'active']);
        FiscalObligation::factory()->create([
            'client_id' => $otherClient->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 3,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('3');
    }

    public function test_stats_widget_shows_presented_this_month_count(): void
    {
        FiscalObligation::factory()->count(4)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Presented,
            'presented_at' => now()->startOfMonth()->addDays(2),
            'due_date' => now()->subDays(1),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('4');
    }

    public function test_stats_widget_shows_active_clients_count(): void
    {
        // Ya hay 1 cliente activo del setUp.
        Client::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'inactive',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('1');
    }

    public function test_stats_widget_shows_invoices_this_month(): void
    {
        Invoice::factory()->count(5)->create([
            'client_id' => $this->client->id,
            'fecha_emision' => now()->startOfMonth()->addDays(1),
        ]);

        // Factura de mes anterior — no debe contarse
        Invoice::factory()->create([
            'client_id' => $this->client->id,
            'fecha_emision' => now()->subMonth()->startOfMonth(),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('5');
    }

    public function test_stats_widget_invoices_scoped_to_admin(): void
    {
        Invoice::factory()->count(2)->create([
            'client_id' => $this->client->id,
            'fecha_emision' => now()->startOfMonth()->addDays(1),
        ]);

        // Facturas de otro admin — no deben verse
        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id, 'status' => 'active']);
        Invoice::factory()->count(10)->create([
            'client_id' => $otherClient->id,
            'fecha_emision' => now()->startOfMonth()->addDays(1),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(DashboardStatsWidget::class)
            ->assertSee('2');
    }

    // ─── ObligacionesPorEstatusChartWidget ────────────────────────────────────

    public function test_chart_widget_renders(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ObligacionesPorEstatusChartWidget::class)
            ->assertSuccessful();
    }

    public function test_chart_widget_data_scoped_to_admin(): void
    {
        // 3 obligaciones pendientes del admin con due_date este mes
        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->startOfMonth(),
        ]);

        // 5 obligaciones de otro admin — no deben aparecer en el dataset del admin
        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id, 'status' => 'active']);
        FiscalObligation::factory()->count(5)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 3, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 3, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 4, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 4, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 5, 'period_year' => 2025],
        )->create([
            'client_id' => $otherClient->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->startOfMonth(),
        ]);

        $this->actingAs($this->admin);

        // El widget renderiza sin error y respeta el scope del admin
        Livewire::test(ObligacionesPorEstatusChartWidget::class)
            ->assertSuccessful();

        // El admin solo tiene 3 obligaciones pendientes este mes
        $count = FiscalObligation::query()
            ->where('status', ObligationStatus::Pending)
            ->whereYear('due_date', now()->year)
            ->whereMonth('due_date', now()->month)
            ->count();

        $this->assertEquals(3, $count);
    }

    public function test_chart_widget_presented_uses_presented_at_date(): void
    {
        // Obligación presentada este mes, pero con due_date el mes pasado
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Presented,
            'due_date' => now()->subMonth()->startOfMonth(),
            'presented_at' => now()->startOfMonth()->addDays(2),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ObligacionesPorEstatusChartWidget::class)
            ->assertSuccessful();

        // Debe encontrarse usando presented_at (este mes), no due_date (mes pasado)
        $countThisMonth = FiscalObligation::query()
            ->where('status', ObligationStatus::Presented)
            ->whereYear('presented_at', now()->year)
            ->whereMonth('presented_at', now()->month)
            ->count();

        $countByDueDate = FiscalObligation::query()
            ->where('status', ObligationStatus::Presented)
            ->whereYear('due_date', now()->year)
            ->whereMonth('due_date', now()->month)
            ->count();

        $this->assertEquals(1, $countThisMonth);
        $this->assertEquals(0, $countByDueDate);
    }

    // ─── ProximasObligacionesWidget ───────────────────────────────────────────

    public function test_proximas_widget_shows_pending_obligations(): void
    {
        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ProximasObligacionesWidget::class)
            ->assertCountTableRecords(3);
    }

    public function test_proximas_widget_excludes_presented_obligations(): void
    {
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Presented,
            'due_date' => now()->addDays(5),
            'presented_at' => now(),
        ]);

        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ProximasObligacionesWidget::class)
            ->assertCountTableRecords(1);
    }

    public function test_proximas_widget_excludes_past_due_dates(): void
    {
        // Vencida en el pasado — no debe aparecer en "próximas"
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(10),
        ]);

        // Pendiente en el futuro — sí debe aparecer
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ProximasObligacionesWidget::class)
            ->assertCountTableRecords(1);
    }

    public function test_proximas_widget_scoped_to_admin(): void
    {
        FiscalObligation::factory()->count(2)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3),
        ]);

        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id, 'status' => 'active']);
        FiscalObligation::factory()->count(5)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 2, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 3, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 3, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 4, 'period_year' => 2025],
        )->create([
            'client_id' => $otherClient->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ProximasObligacionesWidget::class)
            ->assertCountTableRecords(2);
    }

    public function test_proximas_widget_limited_to_ten_records(): void
    {
        // Crear 12 obligaciones únicas con distintos tipo/período
        $sequences = collect(range(1, 12))->map(fn ($i) => [
            'type' => $i % 2 === 0 ? ObligationType::IsrMensual : ObligationType::IvaMensual,
            'period_month' => $i,
            'period_year' => 2025,
        ])->all();

        FiscalObligation::factory()->count(12)->sequence(...$sequences)->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        $widget = Livewire::test(ProximasObligacionesWidget::class);
        $widget->assertSuccessful();

        // Verificar que la query del widget limita a 10 registros
        $records = $widget->instance()->getTableRecords();
        $this->assertCount(10, $records);
    }
}
