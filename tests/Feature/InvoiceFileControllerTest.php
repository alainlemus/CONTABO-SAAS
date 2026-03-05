<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceFileControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $this->admin->id]);
    }

    public function test_guest_cannot_download_xml(): void
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        $this->get(route('invoices.download.xml', $invoice))
            ->assertRedirect('/admin/login');
    }

    public function test_guest_cannot_download_pdf(): void
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        $this->get(route('invoices.download.pdf', $invoice))
            ->assertRedirect('/admin/login');
    }

    public function test_owner_can_download_xml_when_file_exists(): void
    {
        $path = 'invoices/xml/test-factura.xml';
        Storage::disk('local')->put($path, '<xml>contenido</xml>');

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => $path,
            'serie' => 'A',
            'folio' => '0001',
            'uuid' => '12345678-0000-0000-0000-000000000000',
        ]);

        $this->actingAs($this->admin)
            ->get(route('invoices.download.xml', $invoice))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    public function test_owner_gets_404_when_xml_file_missing_from_disk(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => 'invoices/xml/nonexistent.xml',
        ]);

        // El archivo NO existe en el disco falso
        $this->actingAs($this->admin)
            ->get(route('invoices.download.xml', $invoice))
            ->assertNotFound();
    }

    public function test_owner_gets_404_when_xml_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => null,
        ]);

        $this->actingAs($this->admin)
            ->get(route('invoices.download.xml', $invoice))
            ->assertNotFound();
    }

    public function test_other_admin_cannot_download_xml_of_another_owner(): void
    {
        $path = 'invoices/xml/ajena.xml';
        Storage::disk('local')->put($path, '<xml>ajena</xml>');

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => $path,
        ]);

        $otherAdmin = User::factory()->create();

        $this->actingAs($otherAdmin)
            ->get(route('invoices.download.xml', $invoice))
            ->assertForbidden();
    }

    public function test_owner_can_download_pdf_when_file_exists(): void
    {
        $path = 'invoices/pdf/test-factura.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 contenido');

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => $path,
            'xml_path' => null,
            'serie' => 'B',
            'folio' => '0002',
            'uuid' => '87654321-0000-0000-0000-000000000000',
        ]);

        $this->actingAs($this->admin)
            ->get(route('invoices.download.pdf', $invoice))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    public function test_owner_gets_404_when_pdf_file_missing_from_disk(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => 'invoices/pdf/nonexistent.pdf',
        ]);

        $this->actingAs($this->admin)
            ->get(route('invoices.download.pdf', $invoice))
            ->assertNotFound();
    }

    public function test_owner_gets_404_when_pdf_path_is_null(): void
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => null,
        ]);

        $this->actingAs($this->admin)
            ->get(route('invoices.download.pdf', $invoice))
            ->assertNotFound();
    }

    public function test_other_admin_cannot_download_pdf_of_another_owner(): void
    {
        $path = 'invoices/pdf/ajena.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 ajena');

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'pdf_path' => $path,
        ]);

        $otherAdmin = User::factory()->create();

        $this->actingAs($otherAdmin)
            ->get(route('invoices.download.pdf', $invoice))
            ->assertForbidden();
    }

    public function test_capturista_of_same_admin_can_download_xml(): void
    {
        $path = 'invoices/xml/capturista-test.xml';
        Storage::disk('local')->put($path, '<xml>contenido</xml>');

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'xml_path' => $path,
        ]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(route('invoices.download.xml', $invoice))
            ->assertSuccessful();
    }
}
