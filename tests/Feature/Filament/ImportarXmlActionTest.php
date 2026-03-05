<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Invoices\Pages\ListInvoices;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ImportarXmlActionTest extends TestCase
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

    private function cfdiXml(string $rfcEmisor = 'ETE900101AAA', string $rfcReceptor = 'XAXX010101000', string $uuid = '6128e9b2-1234-5678-abcd-ef0123456789'): string
    {
        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <cfdi:Comprobante
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
            TipoDeComprobante="I"
            Serie="A"
            Folio="1"
            Fecha="2024-06-15T10:00:00"
            MetodoPago="PUE"
            FormaPago="03"
            Moneda="MXN"
            SubTotal="1000.00"
            Descuento="0.00"
            Total="1160.00">
          <cfdi:Emisor Rfc="{$rfcEmisor}" Nombre="Empresa Test SA de CV" />
          <cfdi:Receptor Rfc="{$rfcReceptor}" Nombre="Publico en General" UsoCFDI="G03" />
          <cfdi:Conceptos>
            <cfdi:Concepto Descripcion="Servicios de consultoría" />
          </cfdi:Conceptos>
          <cfdi:Impuestos TotalImpuestosTrasladados="160.00" />
          <tfd:TimbreFiscalDigital UUID="{$uuid}" />
        </cfdi:Comprobante>
        XML;
    }

    private function xmlFile(string $content, string $name = 'factura.xml'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    public function test_importar_xml_action_appears_on_list_page(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->assertActionExists('importar_xml');
    }

    public function test_importa_un_xml_valido_y_crea_factura(): void
    {
        $archivo = $this->xmlFile($this->cfdiXml());

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->callAction('importar_xml', data: [
                'client_id' => $this->client->id,
                'type' => 'gasto',
                'xml_files' => [$archivo],
            ]);

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'type' => 'gasto',
            'rfc_emisor' => 'ETE900101AAA',
            'rfc_receptor' => 'XAXX010101000',
            'status' => 'procesado',
            'uuid' => '6128e9b2-1234-5678-abcd-ef0123456789',
        ]);

        $this->assertSame(1, Invoice::count());
    }

    public function test_importa_multiples_xmls(): void
    {
        $archivos = [
            $this->xmlFile($this->cfdiXml(uuid: 'aaaaaaaa-0000-0000-0000-000000000001'), 'factura1.xml'),
            $this->xmlFile($this->cfdiXml('RFC111111AAA', 'RFC222222BBB', 'aaaaaaaa-0000-0000-0000-000000000002'), 'factura2.xml'),
        ];

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->callAction('importar_xml', data: [
                'client_id' => $this->client->id,
                'type' => 'ingreso',
                'xml_files' => $archivos,
            ]);

        $this->assertSame(2, Invoice::where('client_id', $this->client->id)->count());
    }

    public function test_xml_invalido_crea_factura_con_status_error(): void
    {
        $archivo = $this->xmlFile('<esto no es xml valido>>>', 'malo.xml');

        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->callAction('importar_xml', data: [
                'client_id' => $this->client->id,
                'type' => 'gasto',
                'xml_files' => [$archivo],
            ]);

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'type' => 'gasto',
            'status' => 'error',
        ]);
    }

    public function test_action_requiere_client_id_y_type(): void
    {
        Livewire::actingAs($this->admin)
            ->test(ListInvoices::class)
            ->callAction('importar_xml', data: [
                'client_id' => null,
                'type' => null,
                'xml_files' => [],
            ])
            ->assertHasErrors([
                'mountedActions.0.data.client_id',
                'mountedActions.0.data.type',
                'mountedActions.0.data.xml_files',
            ]);

        $this->assertSame(0, Invoice::count());
    }
}
