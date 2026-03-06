<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Exports\FiscalObligationExporter;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class FiscalObligationExporterTest extends TestCase
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
            'tax_regime' => '601',
            'status' => 'active',
        ]);
    }

    public function test_exporter_defines_expected_columns(): void
    {
        $columns = FiscalObligationExporter::getColumns();

        $columnNames = array_map(fn ($col) => $col->getName(), $columns);

        $this->assertContains('client.name', $columnNames);
        $this->assertContains('type', $columnNames);
        $this->assertContains('period_label', $columnNames);
        $this->assertContains('due_date', $columnNames);
        $this->assertContains('status', $columnNames);
        $this->assertContains('presented_at', $columnNames);
        $this->assertContains('reference', $columnNames);
        $this->assertContains('notes', $columnNames);
        $this->assertCount(8, $columns);
    }

    public function test_exporter_get_completed_notification_body_singular(): void
    {
        $export = new Export;
        $export->successful_rows = 1;
        $export->exporter = FiscalObligationExporter::class;

        $body = FiscalObligationExporter::getCompletedNotificationBody($export);

        $this->assertStringContainsString('1', $body);
        $this->assertStringContainsString('registro', $body);
    }

    public function test_exporter_get_completed_notification_body_plural(): void
    {
        $export = new Export;
        $export->successful_rows = 15;
        $export->exporter = FiscalObligationExporter::class;

        $body = FiscalObligationExporter::getCompletedNotificationBody($export);

        $this->assertStringContainsString('15', $body);
        $this->assertStringContainsString('registros', $body);
    }

    public function test_exporter_get_file_name_contains_date(): void
    {
        $export = new Export;
        $export->successful_rows = 5;
        $export->exporter = FiscalObligationExporter::class;
        $export->created_at = now();

        $exporter = new FiscalObligationExporter($export, [], []);

        $fileName = $exporter->getFileName($export);

        $this->assertStringStartsWith('obligaciones-fiscales-', $fileName);
        $this->assertStringContainsString(now()->format('Y-m-d'), $fileName);
    }

    public function test_export_action_exists_on_list_page(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(ListFiscalObligations::class);

        $toolbarActions = $component->instance()->getTable()->getToolbarActions();

        // getToolbarActions() puede retornar Action y BulkActionGroup — filtramos solo Action
        $actionNames = collect($toolbarActions)
            ->filter(fn ($action) => $action instanceof Action)
            ->map(fn ($action) => $action->getName())
            ->all();

        $this->assertContains('export', $actionNames);
    }

    public function test_export_action_creates_export_record_for_csv(): void
    {
        Bus::fake();

        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 2, 'period_year' => 2025],
            ['type' => ObligationType::IsrMensual, 'period_month' => 3, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        // El columnMap debe tener al menos una columna con isEnabled=true para que
        // CanExportRecords no cancele el export silenciosamente.
        $columnMap = collect(FiscalObligationExporter::getColumns())
            ->mapWithKeys(fn ($col) => [
                $col->getName() => ['isEnabled' => true, 'label' => $col->getLabel()],
            ])
            ->all();

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('export', data: [
                'format' => 'csv',
                'columnMap' => $columnMap,
            ]);

        // El ExportAction crea un registro Export antes de despachar los jobs
        $this->assertDatabaseHas('exports', [
            'exporter' => FiscalObligationExporter::class,
            'user_id' => $this->admin->id,
            'total_rows' => 3,
        ]);
    }

    public function test_export_action_creates_export_record_for_xlsx(): void
    {
        Bus::fake();

        FiscalObligation::factory()->count(2)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        $columnMap = collect(FiscalObligationExporter::getColumns())
            ->mapWithKeys(fn ($col) => [
                $col->getName() => ['isEnabled' => true, 'label' => $col->getLabel()],
            ])
            ->all();

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('export', data: [
                'format' => 'xlsx',
                'columnMap' => $columnMap,
            ]);

        // El ExportAction crea un registro Export antes de despachar los jobs
        $this->assertDatabaseHas('exports', [
            'exporter' => FiscalObligationExporter::class,
            'user_id' => $this->admin->id,
            'total_rows' => 2,
        ]);
    }

    public function test_status_column_formats_correctly(): void
    {
        $columns = FiscalObligationExporter::getColumns();
        $statusColumn = collect($columns)->firstWhere(fn ($col) => $col->getName() === 'status');

        $this->assertNotNull($statusColumn);
        $this->assertEquals('Estatus', $statusColumn->getLabel());
    }

    public function test_type_column_formats_correctly(): void
    {
        $columns = FiscalObligationExporter::getColumns();
        $typeColumn = collect($columns)->firstWhere(fn ($col) => $col->getName() === 'type');

        $this->assertNotNull($typeColumn);
        $this->assertEquals('Tipo de obligación', $typeColumn->getLabel());
    }
}
