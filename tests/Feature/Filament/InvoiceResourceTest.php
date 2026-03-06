<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\Resources\Invoices\Pages\EditInvoice;
use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $this->admin->id]);
    }

    public function test_list_invoices_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(InvoiceResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_list_invoices_shows_invoices(): void
    {
        $invoices = Invoice::factory()->count(3)->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertCanSeeTableRecords($invoices);
    }

    public function test_create_invoice_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(InvoiceResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_create_invoice(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateInvoice::class)
            ->fillForm([
                'client_id' => $this->client->id,
                'type' => 'gasto',
                'status' => 'procesado',
                'fecha_emision' => '2024-06-15',
                'rfc_emisor' => 'ETE900101AAA',
                'rfc_receptor' => 'XAXX010101000',
                'tipo_comprobante' => 'I',
                'moneda' => 'MXN',
                'subtotal' => 1000.00,
                'iva' => 160.00,
                'total' => 1160.00,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'type' => 'gasto',
            'rfc_emisor' => 'ETE900101AAA',
        ]);
    }

    public function test_create_invoice_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateInvoice::class)
            ->fillForm([
                'client_id' => null,
                'type' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['client_id', 'type']);
    }

    public function test_edit_invoice_page_loads(): void
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        $this->actingAs($this->admin)
            ->get(InvoiceResource::getUrl('edit', ['record' => $invoice]))
            ->assertSuccessful();
    }

    public function test_can_edit_invoice(): void
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id, 'type' => 'gasto']);

        Livewire::actingAs($this->admin)
            ->test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
            ->fillForm(['type' => 'ingreso'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'type' => 'ingreso',
        ]);
    }

    public function test_can_delete_invoice(): void
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->callTableBulkAction('delete', [$invoice->id]);

        $this->assertModelMissing($invoice);
    }

    public function test_table_filters_by_type(): void
    {
        $ingreso = Invoice::factory()->ingreso()->create(['client_id' => $this->client->id]);
        $gasto = Invoice::factory()->gasto()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->filterTable('type', 'ingreso')
            ->assertCanSeeTableRecords([$ingreso])
            ->assertCanNotSeeTableRecords([$gasto]);
    }

    public function test_download_xml_action_visible_when_xml_path_exists(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => 'invoices/xml/test.xml',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertTableActionVisible('download_xml', $invoice);
    }

    public function test_download_xml_action_hidden_when_xml_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertTableActionHidden('download_xml', $invoice);
    }

    public function test_download_pdf_action_visible_when_pdf_path_exists(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => 'invoices/pdf/test.pdf',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertTableActionVisible('download_pdf', $invoice);
    }

    public function test_download_pdf_action_hidden_when_pdf_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertTableActionHidden('download_pdf', $invoice);
    }

    public function test_edit_page_shows_download_xml_action_when_xml_path_exists(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => 'invoices/xml/test.xml',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
            ->assertActionVisible('download_xml');
    }

    public function test_edit_page_hides_download_xml_action_when_xml_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
            ->assertActionHidden('download_xml');
    }

    public function test_edit_page_shows_download_pdf_action_when_pdf_path_exists(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => 'invoices/pdf/test.pdf',
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
            ->assertActionVisible('download_pdf');
    }

    public function test_edit_page_hides_download_pdf_action_when_pdf_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => null,
        ]);

        Livewire::actingAs($this->admin)
            ->test(EditInvoice::class, ['record' => $invoice->getRouteKey()])
            ->assertActionHidden('download_pdf');
    }

    public function test_list_does_not_show_invoices_from_other_users(): void
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);
        $otherInvoice = Invoice::factory()->create(['client_id' => $otherClient->id]);

        $myInvoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertCanSeeTableRecords([$myInvoice])
            ->assertCanNotSeeTableRecords([$otherInvoice]);
    }

    public function test_list_page_loads_for_new_user_without_data(): void
    {
        $newUser = User::factory()->create();

        $this->actingAs($newUser)
            ->get(InvoiceResource::getUrl('index'))
            ->assertSuccessful();
    }
}
