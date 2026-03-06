<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Widgets\ClientesEnRiesgoWidget;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientesEnRiesgoWidgetTest extends TestCase
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

    public function test_widget_shows_clients_with_overdue_obligations(): void
    {
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertCountTableRecords(1)
            ->assertSee($this->client->name);
    }

    public function test_widget_excludes_clients_without_overdue_obligations(): void
    {
        // Cliente sin obligaciones vencidas (solo pendiente)
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertCountTableRecords(0);
    }

    public function test_widget_excludes_clients_with_only_presented_obligations(): void
    {
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Presented,
            'due_date' => now()->subDays(10),
            'presented_at' => now()->subDays(1),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertCountTableRecords(0);
    }

    public function test_widget_shows_overdue_count_per_client(): void
    {
        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertSee('3');
    }

    public function test_widget_scoped_to_admin(): void
    {
        // Obligación vencida del admin principal
        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        // Cliente de otro admin con obligación vencida — NO debe aparecer
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

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertCountTableRecords(1)
            ->assertSee($this->client->name)
            ->assertDontSee($otherClient->name);
    }

    public function test_widget_shows_multiple_at_risk_clients(): void
    {
        $client2 = Client::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'active',
        ]);

        FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        FiscalObligation::factory()->create([
            'client_id' => $client2->id,
            'type' => ObligationType::IvaMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(3),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertCountTableRecords(2);
    }

    public function test_widget_shows_empty_state_when_no_clients_at_risk(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ClientesEnRiesgoWidget::class)
            ->assertSee('Sin clientes en riesgo');
    }
}
