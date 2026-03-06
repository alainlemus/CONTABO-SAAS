<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Widgets\CalendarioVencimientosWidget;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarioVencimientosWidgetTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Client::flushEventListeners();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'active',
        ]);
    }

    public function test_widget_renders_successfully(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertSuccessful();
    }

    public function test_widget_shows_pending_obligations_within_30_days(): void
    {
        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(3);
    }

    public function test_widget_excludes_obligations_beyond_30_days(): void
    {
        // Dentro de los 30 días — sí aparece
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        // Más de 30 días — no debe aparecer
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_month' => 2,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(45),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(1);
    }

    public function test_widget_excludes_obligations_with_past_due_dates(): void
    {
        // Vencida en el pasado — no debe aparecer (fuera del rango [hoy, +30])
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

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(1);
    }

    public function test_widget_excludes_presented_obligations(): void
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

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(1);
    }

    public function test_widget_scoped_to_current_admin(): void
    {
        FiscalObligation::factory()->count(2)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
        ]);

        // Obligaciones de otro admin — no deben verse
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
            'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(2);
    }

    public function test_widget_shows_empty_state_when_no_obligations(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertSee('Sin vencimientos en los próximos 30 días');
    }

    public function test_widget_includes_today_due_date(): void
    {
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(CalendarioVencimientosWidget::class)
            ->assertCountTableRecords(1);
    }
}
