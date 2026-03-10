<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationType;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Resources\Team\TeamMemberResource;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    // ─── Dashboard (solo Admin) ───────────────────────────────────────────────

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get(Dashboard::getUrl())
            ->assertSuccessful();
    }

    public function test_capturista_cannot_access_dashboard(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(Dashboard::getUrl())
            ->assertRedirect();
    }

    public function test_viewer_cannot_access_dashboard(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(Dashboard::getUrl())
            ->assertRedirect();
    }

    // ─── TeamMemberResource (solo Admin) ─────────────────────────────────────

    public function test_viewer_cannot_access_team_resource(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(TeamMemberResource::getUrl('index'))
            ->assertForbidden();
    }

    // ─── Viewer — Clientes (solo lectura) ────────────────────────────────────

    public function test_viewer_can_list_clients(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(ClientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_viewer_cannot_access_create_client_page(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(ClientResource::getUrl('create'))
            ->assertForbidden();
    }

    public function test_viewer_cannot_access_edit_client_page(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(ClientResource::getUrl('edit', ['record' => $client]))
            ->assertForbidden();
    }

    public function test_viewer_does_not_see_edit_action_in_clients_table(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListClients::class)
            ->assertTableActionHidden('edit', $client);
    }

    public function test_viewer_does_not_see_quick_note_action_in_clients_table(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListClients::class)
            ->assertTableActionHidden('quick_note', $client);
    }

    public function test_viewer_does_not_see_delete_bulk_action_in_clients_table(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListClients::class)
            ->assertTableBulkActionHidden('delete');
    }

    // ─── Viewer — Facturas (solo lectura) ────────────────────────────────────

    public function test_viewer_can_list_invoices(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(InvoiceResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_viewer_cannot_access_create_invoice_page(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(InvoiceResource::getUrl('create'))
            ->assertForbidden();
    }

    public function test_viewer_does_not_see_import_xml_action(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListInvoices::class)
            ->assertActionHidden('importar_xml');
    }

    public function test_viewer_does_not_see_delete_bulk_action_in_invoices_table(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListInvoices::class)
            ->assertTableBulkActionHidden('delete');
    }

    // ─── Viewer — Obligaciones Fiscales (solo lectura) ────────────────────────

    public function test_viewer_can_list_fiscal_obligations(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(FiscalObligationResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_viewer_cannot_access_create_fiscal_obligation_page(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(FiscalObligationResource::getUrl('create'))
            ->assertForbidden();
    }

    public function test_viewer_does_not_see_edit_action_in_fiscal_obligations_table(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableActionHidden('edit', $obligation);
    }

    public function test_viewer_does_not_see_mark_presented_action_in_fiscal_obligations_table(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableActionHidden('mark_presented', $obligation);
    }

    public function test_viewer_does_not_see_generate_current_month_action(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableActionHidden('generate_current_month');
    }

    public function test_viewer_does_not_see_delete_bulk_action_in_fiscal_obligations_table(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableBulkActionHidden('delete');
    }

    // ─── Capturista — puede crear/editar, no puede ver Dashboard ni Equipo ────

    public function test_capturista_can_list_clients(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(ClientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_capturista_can_access_create_client_page(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(ClientResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_capturista_sees_edit_action_in_clients_table(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListClients::class)
            ->assertTableActionVisible('edit', $client);
    }

    public function test_capturista_sees_mark_presented_action_for_pending_obligation(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListFiscalObligations::class)
            ->assertTableActionVisible('mark_presented', $obligation);
    }

    public function test_capturista_does_not_see_delete_bulk_action_in_clients_table(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListClients::class)
            ->assertTableBulkActionVisible('delete');
    }

    public function test_viewer_does_not_see_bulk_mark_presented_in_fiscal_obligations(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableBulkActionHidden('mark_presented_bulk');
    }

    public function test_viewer_does_not_see_bulk_mark_not_applicable_in_fiscal_obligations(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListFiscalObligations::class)
            ->assertTableBulkActionHidden('mark_not_applicable_bulk');
    }

    // ─── Exportar — todos los roles pueden exportar ──────────────────────────

    public function test_viewer_sees_export_action_in_invoices(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListInvoices::class)
            ->assertActionVisible('export');
    }

    public function test_capturista_sees_export_action_in_invoices(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListInvoices::class)
            ->assertActionVisible('export');
    }

    public function test_admin_sees_export_action_in_invoices(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertActionVisible('export');
    }

    public function test_viewer_sees_compliance_report_export_in_clients(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListClients::class)
            ->assertActionVisible('compliance_report');
    }

    public function test_capturista_sees_compliance_report_export_in_clients(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListClients::class)
            ->assertActionVisible('compliance_report');
    }

    // ─── Viewer — CreateAction oculto en Clientes y Facturas ─────────────────

    public function test_viewer_does_not_see_create_action_in_clients(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListClients::class)
            ->assertActionHidden('create');
    }

    public function test_viewer_does_not_see_create_action_in_invoices(): void
    {
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($viewer)
            ->test(ListInvoices::class)
            ->assertActionHidden('create');
    }
}
