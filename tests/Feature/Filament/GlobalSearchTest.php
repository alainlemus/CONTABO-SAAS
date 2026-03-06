<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
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
            'name' => 'Empresa Ejemplo SA de CV',
            'tax_id' => 'EEJ010101ABC',
            'email' => 'contacto@empresa.mx',
            'tax_regime' => '601',
            'status' => 'active',
        ]);
    }

    // ─── ClientResource Global Search ─────────────────────────────────────────

    public function test_client_resource_has_record_title_attribute(): void
    {
        $this->assertEquals('name', ClientResource::getRecordTitleAttribute());
    }

    public function test_client_resource_globally_searchable_attributes(): void
    {
        $attributes = ClientResource::getGloballySearchableAttributes();

        $this->assertContains('name', $attributes);
        $this->assertContains('tax_id', $attributes);
        $this->assertContains('email', $attributes);
    }

    public function test_client_global_search_result_details_returns_rfc_and_regime(): void
    {
        $details = ClientResource::getGlobalSearchResultDetails($this->client);

        $this->assertArrayHasKey('RFC', $details);
        $this->assertArrayHasKey('Régimen', $details);
        $this->assertEquals('EEJ010101ABC', $details['RFC']);
        $this->assertStringContainsString('601', $details['Régimen']);
    }

    public function test_client_global_search_result_details_with_no_regime(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => null,
        ]);

        $details = ClientResource::getGlobalSearchResultDetails($client);

        $this->assertEquals('—', $details['Régimen']);
    }

    // ─── FiscalObligationResource Global Search ───────────────────────────────

    public function test_fiscal_obligation_resource_record_title_returns_type_label(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
        ]);

        $title = FiscalObligationResource::getRecordTitle($obligation);

        $this->assertIsString($title);
        $this->assertEquals($obligation->type->label(), $title);
    }

    public function test_fiscal_obligation_resource_globally_searchable_attributes(): void
    {
        $attributes = FiscalObligationResource::getGloballySearchableAttributes();

        $this->assertContains('client.name', $attributes);
    }

    public function test_fiscal_obligation_global_search_result_details(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'period_month' => 3,
            'period_year' => 2025,
        ]);

        $details = FiscalObligationResource::getGlobalSearchResultDetails($obligation);

        $this->assertArrayHasKey('Cliente', $details);
        $this->assertArrayHasKey('Período', $details);
        $this->assertEquals('Empresa Ejemplo SA de CV', $details['Cliente']);
    }

    // ─── Dashboard label ──────────────────────────────────────────────────────

    public function test_dashboard_page_has_panel_kpis_navigation_label(): void
    {
        $this->assertEquals('Panel KPIs', \App\Filament\Pages\Dashboard::getNavigationLabel());
    }

    public function test_dashboard_page_has_panel_kpis_title(): void
    {
        $page = new \App\Filament\Pages\Dashboard;

        $this->assertEquals('Panel KPIs', $page->getTitle());
    }
}
