<?php

namespace Tests\Feature\Filament\Clients;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Pages\ViewClient;
use App\Filament\Resources\Clients\RelationManagers\FiscalObligationsRelationManager;
use App\Filament\Resources\Clients\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\Clients\RelationManagers\NotesRelationManager;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ViewClientTest extends TestCase
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
            'name' => 'Empresa de Prueba SA de CV',
            'tax_id' => 'EPR123456ABC',
            'tax_regime' => '601',
            'person_type' => 'moral',
            'status' => 'active',
        ]);
    }

    public function test_view_page_renders_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(ViewClient::getUrl(['record' => $this->client]))
            ->assertSuccessful();
    }

    public function test_view_page_shows_client_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(ViewClient::getUrl(['record' => $this->client]));

        $response->assertSuccessful()
            ->assertSee('Empresa de Prueba SA de CV')
            ->assertSee('EPR123456ABC')
            ->assertSee('General de Ley');
    }

    public function test_edit_action_exists_on_view_page(): void
    {
        $this->actingAs($this->admin)
            ->get(ViewClient::getUrl(['record' => $this->client]))
            ->assertSuccessful()
            ->assertSee('Editar');
    }

    public function test_view_page_is_registered_in_resource(): void
    {
        $pages = ClientResource::getPages();

        $this->assertArrayHasKey('view', $pages);
    }

    public function test_fiscal_obligations_relation_manager_visible(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FiscalObligationsRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertCanSeeTableRecords([$obligation]);
    }

    public function test_fiscal_obligations_relation_manager_has_no_create_action(): void
    {
        Livewire::actingAs($this->admin)
            ->test(FiscalObligationsRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertTableActionDoesNotExist('create');
    }

    public function test_fiscal_obligations_does_not_show_other_clients_records(): void
    {
        $otherClient = Client::factory()->create(['user_id' => $this->admin->id]);
        $otherObligation = FiscalObligation::factory()->create([
            'client_id' => $otherClient->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(FiscalObligationsRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertCanNotSeeTableRecords([$otherObligation]);
    }

    public function test_invoices_relation_manager_visible(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(InvoicesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertCanSeeTableRecords([$invoice]);
    }

    public function test_invoices_relation_manager_has_no_create_action(): void
    {
        Livewire::actingAs($this->admin)
            ->test(InvoicesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertTableActionDoesNotExist('create');
    }

    public function test_invoices_does_not_show_other_clients_records(): void
    {
        $otherClient = Client::factory()->create(['user_id' => $this->admin->id]);
        $otherInvoice = Invoice::factory()->create([
            'client_id' => $otherClient->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(InvoicesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertCanNotSeeTableRecords([$otherInvoice]);
    }

    public function test_notes_relation_manager_visible(): void
    {
        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => ViewClient::class,
            ])
            ->assertSuccessful();
    }
}
