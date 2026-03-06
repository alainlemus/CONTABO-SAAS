<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Filament\Resources\FiscalObligations\Pages\CreateFiscalObligation;
use App\Filament\Resources\FiscalObligations\Pages\EditFiscalObligation;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FiscalObligationResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        Client::flushEventListeners();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => '601',
            'status' => 'active',
        ]);
    }

    // ─── Acceso a páginas ─────────────────────────────────────────────────────

    public function test_list_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(FiscalObligationResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(FiscalObligationResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_edit_page_loads(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $this->actingAs($this->admin)
            ->get(FiscalObligationResource::getUrl('edit', ['record' => $obligation]))
            ->assertSuccessful();
    }

    // ─── Visibilidad multi-tenant ─────────────────────────────────────────────

    public function test_list_shows_only_own_obligations(): void
    {
        $ownObligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $otherAdmin = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherAdmin->id]);
        $otherObligation = FiscalObligation::factory()->create([
            'client_id' => $otherClient->id,
            'type' => ObligationType::IvaMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->assertCanSeeTableRecords([$ownObligation])
            ->assertCanNotSeeTableRecords([$otherObligation]);
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function test_can_create_fiscal_obligation(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateFiscalObligation::class)
            ->fillForm([
                'client_id' => $this->client->id,
                'type' => ObligationType::IsrMensual->value,
                'status' => ObligationStatus::Pending->value,
                'period_year' => 2025,
                'period_month' => 3,
                'due_date' => '2025-04-17',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('fiscal_obligations', [
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual->value,
            'period_year' => 2025,
            'period_month' => 3,
            'status' => ObligationStatus::Pending->value,
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateFiscalObligation::class)
            ->fillForm([
                'client_id' => null,
                'type' => null,
                'period_year' => null,
                'due_date' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['client_id', 'type', 'period_year', 'due_date']);
    }

    public function test_can_edit_fiscal_obligation(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
            'status' => ObligationStatus::Pending,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditFiscalObligation::class, ['record' => $obligation->getRouteKey()])
            ->fillForm([
                'status' => ObligationStatus::Presented->value,
                'presented_at' => '2025-02-15',
                'reference' => 'REF-12345',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $obligation->id,
            'status' => ObligationStatus::Presented->value,
            'reference' => 'REF-12345',
        ]);
    }

    public function test_can_delete_fiscal_obligation(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('delete', [$obligation]);

        $this->assertModelMissing($obligation);
    }

    // ─── Acción mark_presented ────────────────────────────────────────────────

    public function test_mark_presented_action_visible_for_pending_obligation(): void
    {
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->assertTableActionVisible('mark_presented', $obligation);
    }

    public function test_mark_presented_action_hidden_for_presented_obligation(): void
    {
        $obligation = FiscalObligation::factory()->presented()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->assertTableActionHidden('mark_presented', $obligation);
    }

    public function test_mark_presented_action_updates_status(): void
    {
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => '2025-02-10',
                'reference' => 'ACUSE-9999',
            ]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $obligation->id,
            'status' => ObligationStatus::Presented->value,
            'reference' => 'ACUSE-9999',
        ]);
    }

    // ─── Filtros ──────────────────────────────────────────────────────────────

    public function test_status_filter_shows_only_matching_obligations(): void
    {
        $pending = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $presented = FiscalObligation::factory()->presented()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->filterTable('status', ObligationStatus::Pending->value)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$presented]);
    }

    // ─── Roles ────────────────────────────────────────────────────────────────

    public function test_capturista_can_view_obligations(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(FiscalObligationResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_capturista_cannot_delete_obligation(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        // La policy niega la eliminación a capturistas
        $this->assertFalse($capturista->can('delete', $obligation));
    }

    // ─── PDF del acuse SAT ────────────────────────────────────────────────────

    public function test_mark_presented_action_saves_acuse_pdf_path(): void
    {
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        // Simula que la obligación ya tiene un acuse pdf guardado previamente
        $existingPath = 'fiscal-obligations/acuses/'.$obligation->id.'/acuse_sat.pdf';
        Storage::disk('local')->put($existingPath, '%PDF-1.4 fake content');
        $obligation->update(['acuse_pdf_path' => $existingPath]);

        // Al marcar presentada sin subir nuevo PDF, el path existente debe conservarse
        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => '2025-02-10',
                'reference' => 'ACUSE-PDF-001',
                'acuse_pdf_path' => null,
            ]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $obligation->id,
            'status' => ObligationStatus::Presented->value,
            'acuse_pdf_path' => $existingPath,
        ]);
    }

    public function test_download_acuse_button_visible_in_edit_when_pdf_exists(): void
    {
        $obligation = FiscalObligation::factory()->withAcuse()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $this->actingAs($this->admin)
            ->get(FiscalObligationResource::getUrl('edit', ['record' => $obligation]))
            ->assertSuccessful()
            ->assertSee('Descargar acuse PDF');
    }

    public function test_download_acuse_button_hidden_in_edit_when_no_pdf(): void
    {
        $obligation = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
            'acuse_pdf_path' => null,
        ]);

        $this->actingAs($this->admin)
            ->get(FiscalObligationResource::getUrl('edit', ['record' => $obligation]))
            ->assertSuccessful()
            ->assertDontSee('Descargar acuse PDF');
    }

    // ─── Bulk Actions ─────────────────────────────────────────────────────────

    public function test_bulk_mark_presented_updates_pending_obligations(): void
    {
        $pending1 = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $pending2 = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('mark_presented_bulk', [$pending1, $pending2]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $pending1->id,
            'status' => ObligationStatus::Presented->value,
        ]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $pending2->id,
            'status' => ObligationStatus::Presented->value,
        ]);
    }

    public function test_bulk_mark_presented_also_works_for_overdue_obligations(): void
    {
        $overdue = FiscalObligation::factory()->overdue()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2024,
            'period_month' => 6,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('mark_presented_bulk', [$overdue]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $overdue->id,
            'status' => ObligationStatus::Presented->value,
        ]);
    }

    public function test_bulk_mark_presented_skips_already_presented_obligations(): void
    {
        $presented = FiscalObligation::factory()->presented()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);

        $originalPresentedAt = $presented->presented_at;

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('mark_presented_bulk', [$presented]);

        // La fecha de presentación no debe cambiar (la acción filtra por pending/overdue)
        $presented->refresh();
        $this->assertEquals(ObligationStatus::Presented, $presented->status);
        $this->assertEquals($originalPresentedAt->toDateTimeString(), $presented->presented_at->toDateTimeString());
    }

    public function test_bulk_mark_not_applicable_updates_pending_obligations(): void
    {
        $pending1 = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 2,
        ]);

        $pending2 = FiscalObligation::factory()->pending()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_year' => 2025,
            'period_month' => 2,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('mark_not_applicable_bulk', [$pending1, $pending2]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $pending1->id,
            'status' => ObligationStatus::NotApplicable->value,
        ]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $pending2->id,
            'status' => ObligationStatus::NotApplicable->value,
        ]);
    }

    public function test_bulk_mark_not_applicable_skips_presented_obligations(): void
    {
        $presented = FiscalObligation::factory()->presented()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 3,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListFiscalObligations::class)
            ->callTableBulkAction('mark_not_applicable_bulk', [$presented]);

        // Debe mantenerse como presentada, no cambiar a no aplica
        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $presented->id,
            'status' => ObligationStatus::Presented->value,
        ]);
    }
}
