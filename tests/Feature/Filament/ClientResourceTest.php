<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\Firm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Firm $firm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->firm = Firm::factory()->create();
    }

    public function test_list_clients_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(ClientResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_list_clients_shows_clients(): void
    {
        $clients = Client::factory()->count(3)->for($this->firm)->create();

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->assertCanSeeTableRecords($clients);
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
                'firm_id'          => $this->firm->id,
                'person_type'      => 'moral',
                'name'             => 'Empresa Test SA de CV',
                'tax_id'           => 'ETE900101AAA',
                'email'            => 'test@empresa.com',
                'status'           => 'active',
                'billing_cycle'    => 'monthly',
                'compliance_level' => 'medium',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'name'   => 'Empresa Test SA de CV',
            'tax_id' => 'ETE900101AAA',
        ]);
    }

    public function test_create_client_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateClient::class)
            ->fillForm([
                'name'   => '',
                'tax_id' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'tax_id']);
    }

    public function test_edit_client_page_loads(): void
    {
        $client = Client::factory()->for($this->firm)->create();

        $this->actingAs($this->admin)
            ->get(ClientResource::getUrl('edit', ['record' => $client]))
            ->assertSuccessful();
    }

    public function test_can_edit_client(): void
    {
        $client = Client::factory()->for($this->firm)->create();

        Livewire::actingAs($this->admin)
            ->test(EditClient::class, ['record' => $client->getRouteKey()])
            ->fillForm([
                'name'   => 'Nombre Actualizado SA de CV',
                'status' => 'inactive',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'id'     => $client->id,
            'name'   => 'Nombre Actualizado SA de CV',
            'status' => 'inactive',
        ]);
    }

    public function test_can_delete_client(): void
    {
        $client = Client::factory()->for($this->firm)->create();

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->callTableBulkAction('delete', [$client]);

        $this->assertModelMissing($client);
    }

    public function test_table_has_status_filter(): void
    {
        $active = Client::factory()->for($this->firm)->create(['status' => 'active']);
        $inactive = Client::factory()->for($this->firm)->create(['status' => 'inactive']);

        Livewire::actingAs($this->admin)
            ->test(ListClients::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$inactive]);
    }
}
