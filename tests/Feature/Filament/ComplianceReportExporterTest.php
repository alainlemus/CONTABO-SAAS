<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Exports\ComplianceReportExporter;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Tests\TestCase;

class ComplianceReportExporterTest extends TestCase
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
            'tax_regime' => '601',
            'status' => 'active',
        ]);
    }

    public function test_exporter_defines_expected_columns(): void
    {
        $columns = ComplianceReportExporter::getColumns();
        $columnNames = array_map(fn ($col) => $col->getName(), $columns);

        $this->assertContains('client.name', $columnNames);
        $this->assertContains('client.tax_id', $columnNames);
        $this->assertContains('client.person_type', $columnNames);
        $this->assertContains('client.tax_regime', $columnNames);
        $this->assertContains('type', $columnNames);
        $this->assertContains('period_label', $columnNames);
        $this->assertContains('due_date', $columnNames);
        $this->assertContains('status', $columnNames);
        $this->assertContains('presented_at', $columnNames);
        $this->assertContains('reference', $columnNames);
        $this->assertCount(10, $columns);
    }

    public function test_exporter_options_form_includes_year_and_month_selects(): void
    {
        $components = ComplianceReportExporter::getOptionsFormComponents();
        $names = array_map(fn ($c) => $c->getName(), $components);

        $this->assertContains('period_year', $names);
        $this->assertContains('period_month', $names);
        $this->assertCount(2, $components);
    }

    public function test_exporter_completed_notification_body_singular(): void
    {
        $export = new Export;
        $export->successful_rows = 1;
        $export->exporter = ComplianceReportExporter::class;

        $body = ComplianceReportExporter::getCompletedNotificationBody($export);

        $this->assertStringContainsString('1', $body);
        $this->assertStringContainsString('registro', $body);
    }

    public function test_exporter_completed_notification_body_plural(): void
    {
        $export = new Export;
        $export->successful_rows = 12;
        $export->exporter = ComplianceReportExporter::class;

        $body = ComplianceReportExporter::getCompletedNotificationBody($export);

        $this->assertStringContainsString('12', $body);
        $this->assertStringContainsString('registros', $body);
    }

    public function test_exporter_file_name_contains_date(): void
    {
        $export = new Export;
        $export->successful_rows = 1;
        $export->exporter = ComplianceReportExporter::class;
        $export->created_at = now();

        $exporter = new ComplianceReportExporter($export, [], []);

        $this->assertStringStartsWith('reporte-cumplimiento-', $exporter->getFileName($export));
        $this->assertStringContainsString(now()->format('Y-m-d'), $exporter->getFileName($export));
    }

    public function test_compliance_report_action_exists_on_list_clients_page(): void
    {
        $this->actingAs($this->admin);

        $component = Livewire::test(ListClients::class);
        $actions = $component->instance()->getCachedHeaderActions();

        $actionNames = collect($actions)
            ->filter(fn ($action) => $action instanceof ExportAction)
            ->map(fn ($action) => $action->getName())
            ->all();

        $this->assertContains('compliance_report', $actionNames);
    }

    public function test_compliance_report_export_creates_export_record(): void
    {
        Bus::fake();

        FiscalObligation::factory()->count(3)->sequence(
            ['type' => ObligationType::IsrMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::IvaMensual, 'period_month' => 1, 'period_year' => 2025],
            ['type' => ObligationType::Diot, 'period_month' => 2, 'period_year' => 2025],
        )->create([
            'client_id' => $this->client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->admin);

        $columnMap = collect(ComplianceReportExporter::getColumns())
            ->mapWithKeys(fn ($col) => [
                $col->getName() => ['isEnabled' => true, 'label' => $col->getLabel()],
            ])
            ->all();

        Livewire::test(ListClients::class)
            ->callAction('compliance_report', data: [
                'format' => 'csv',
                'period_year' => 2025,
                'period_month' => 1,
                'columnMap' => $columnMap,
            ]);

        $this->assertDatabaseHas('exports', [
            'exporter' => ComplianceReportExporter::class,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_person_type_column_label_is_correct(): void
    {
        $columns = ComplianceReportExporter::getColumns();
        $col = collect($columns)->firstWhere(fn ($c) => $c->getName() === 'client.person_type');

        $this->assertNotNull($col);
        $this->assertEquals('Tipo de persona', $col->getLabel());
    }

    public function test_status_column_label_is_correct(): void
    {
        $columns = ComplianceReportExporter::getColumns();
        $col = collect($columns)->firstWhere(fn ($c) => $c->getName() === 'status');

        $this->assertNotNull($col);
        $this->assertEquals('Estatus', $col->getLabel());
    }
}
