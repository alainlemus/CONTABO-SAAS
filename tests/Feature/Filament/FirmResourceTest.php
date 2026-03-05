<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Firms\FirmResource;
use App\Filament\Resources\Firms\Pages\CreateFirm;
use App\Filament\Resources\Firms\Pages\EditFirm;
use App\Filament\Resources\Firms\Pages\ListFirms;
use App\Models\Client;
use App\Models\Firm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FirmResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_list_firms_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(FirmResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_list_firms_shows_firms(): void
    {
        $firms = Firm::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListFirms::class)
            ->assertCanSeeTableRecords($firms);
    }

    public function test_create_firm_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(FirmResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_create_firm(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateFirm::class)
            ->fillForm([
                'name'          => 'Despacho Contable Test',
                'tax_id'        => 'DCT900101AAA',
                'billing_plan'  => 'starter',
                'contact_email' => 'admin@despacho.com',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('firms', [
            'name'   => 'Despacho Contable Test',
            'tax_id' => 'DCT900101AAA',
        ]);
    }

    public function test_create_firm_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateFirm::class)
            ->fillForm([
                'name'   => '',
                'tax_id' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'tax_id']);
    }

    public function test_edit_firm_page_loads(): void
    {
        $firm = Firm::factory()->create();

        $this->actingAs($this->admin)
            ->get(FirmResource::getUrl('edit', ['record' => $firm]))
            ->assertSuccessful();
    }

    public function test_can_edit_firm(): void
    {
        $firm = Firm::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(EditFirm::class, ['record' => $firm->getRouteKey()])
            ->fillForm([
                'name'          => 'Despacho Actualizado SC',
                'billing_plan'  => 'growth',
                'contact_phone' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('firms', [
            'id'           => $firm->id,
            'name'         => 'Despacho Actualizado SC',
            'billing_plan' => 'growth',
        ]);
    }

    public function test_can_delete_firm(): void
    {
        $firm = Firm::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(ListFirms::class)
            ->callTableBulkAction('delete', [$firm]);

        $this->assertModelMissing($firm);
    }

    public function test_firm_counts_clients(): void
    {
        $firm = Firm::factory()->create();
        Client::factory()->count(5)->for($firm)->create();

        Livewire::actingAs($this->admin)
            ->test(ListFirms::class)
            ->assertCanSeeTableRecords([$firm]);

        $this->assertSame(5, $firm->clients()->count());
    }
}
