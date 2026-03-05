<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_list_clients_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(ClientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_list_clients_shows_only_own_clients(): void
    {
        $ownClients = Client::factory()->count(3)->create(['user_id' => $this->admin->id]);
        $otherAdmin = User::factory()->create();
        $otherClients = Client::factory()->count(2)->create(['user_id' => $otherAdmin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertCanSeeTableRecords($ownClients)
            ->assertCanNotSeeTableRecords($otherClients);
    }

    public function test_create_client_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(ClientResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_create_client(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateClient::class)
            ->fillForm([
                'person_type' => 'moral',
                'name' => 'Empresa Test SA de CV',
                'tax_id' => 'ETE900101AAA',
                'email' => 'test@empresa.com',
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'compliance_level' => 'medium',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'name' => 'Empresa Test SA de CV',
            'tax_id' => 'ETE900101AAA',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_create_client_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateClient::class)
            ->fillForm([
                'name' => '',
                'tax_id' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'tax_id']);
    }

    public function test_edit_client_page_loads(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->get(ClientResource::getUrl('edit', ['record' => $client]))
            ->assertSuccessful();
    }

    public function test_can_edit_client(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->fillForm([
                'name' => 'Nombre Actualizado SA de CV',
                'status' => 'inactive',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Nombre Actualizado SA de CV',
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_client(): void
    {
        $client = Client::factory()->create(['user_id' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->callTableBulkAction('delete', [$client]);

        $this->assertModelMissing($client);
    }

    public function test_table_has_status_filter(): void
    {
        $active = Client::factory()->create(['user_id' => $this->admin->id, 'status' => 'active']);
        $inactive = Client::factory()->create(['user_id' => $this->admin->id, 'status' => 'inactive']);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$inactive]);
    }

    // ─── Visibilidad de acciones CER / KEY en tabla ───────────────────────────

    public function test_table_download_cer_action_visible_for_admin_when_path_exists(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => 'clients/efirma/test.cer',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertTableActionVisible('download_cer', $client);
    }

    public function test_table_download_cer_action_hidden_when_path_is_null(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertTableActionHidden('download_cer', $client);
    }

    public function test_table_download_cer_action_hidden_for_capturista(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => 'clients/efirma/test.cer',
        ]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListClients::class)
            ->assertTableActionHidden('download_cer', $client);
    }

    public function test_table_download_key_action_visible_for_admin_when_path_exists(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => 'clients/efirma/test.key',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertTableActionVisible('download_key', $client);
    }

    public function test_table_download_key_action_hidden_when_path_is_null(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertTableActionHidden('download_key', $client);
    }

    public function test_table_download_key_action_hidden_for_capturista(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => 'clients/efirma/test.key',
        ]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(ListClients::class)
            ->assertTableActionHidden('download_key', $client);
    }

    // ─── Visibilidad de acciones CER / KEY en EditClient ─────────────────────

    public function test_edit_page_download_cer_action_visible_for_admin_when_path_exists(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => 'clients/efirma/test.cer',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionVisible('download_cer');
    }

    public function test_edit_page_download_cer_action_hidden_when_path_is_null(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionHidden('download_cer');
    }

    public function test_edit_page_download_cer_action_hidden_for_capturista(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_cer_path' => 'clients/efirma/test.cer',
        ]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionHidden('download_cer');
    }

    public function test_edit_page_download_key_action_visible_for_admin_when_path_exists(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => 'clients/efirma/test.key',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionVisible('download_key');
    }

    public function test_edit_page_download_key_action_hidden_when_path_is_null(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionHidden('download_key');
    }

    public function test_edit_page_download_key_action_hidden_for_capturista(): void
    {
        $client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'efirma_key_path' => 'clients/efirma/test.key',
        ]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($capturista)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->assertActionHidden('download_key');
    }
}
