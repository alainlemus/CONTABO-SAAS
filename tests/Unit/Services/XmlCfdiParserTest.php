<?php

namespace Tests\Unit\Services;

use App\Services\XmlCfdiParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class XmlCfdiParserTest extends TestCase
{
    private XmlCfdiParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new XmlCfdiParser;
    }

    private function cfdiXml(array $overrides = []): string
    {
        $defaults = [
            'TipoDeComprobante' => 'I',
            'Serie' => 'A',
            'Folio' => '123',
            'Fecha' => '2024-06-15T10:30:00',
            'MetodoPago' => 'PUE',
            'FormaPago' => '03',
            'Moneda' => 'MXN',
            'SubTotal' => '1000.00',
            'Descuento' => '0.00',
            'Total' => '1160.00',
        ];

        $attrs = array_merge($defaults, $overrides);
        $attrStr = '';
        foreach ($attrs as $k => $v) {
            $attrStr .= " {$k}=\"{$v}\"";
        }

        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <cfdi:Comprobante
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
            {$attrStr}>
          <cfdi:Emisor Rfc="ETE900101AAA" Nombre="Empresa Test SA de CV" />
          <cfdi:Receptor Rfc="XAXX010101000" Nombre="Publico en General" UsoCFDI="G03" />
          <cfdi:Conceptos>
            <cfdi:Concepto Descripcion="Servicios de consultoría" />
          </cfdi:Conceptos>
          <cfdi:Impuestos TotalImpuestosTrasladados="160.00" />
          <tfd:TimbreFiscalDigital UUID="6128e9b2-1234-5678-abcd-ef0123456789" />
        </cfdi:Comprobante>
        XML;
    }

    public function test_parses_basic_cfdi_fields(): void
    {
        $result = $this->parser->parse($this->cfdiXml());

        $this->assertSame('A', $result['serie']);
        $this->assertSame('123', $result['folio']);
        $this->assertSame('2024-06-15', $result['fecha_emision']);
        $this->assertSame('I', $result['tipo_comprobante']);
        $this->assertSame('PUE', $result['metodo_pago']);
        $this->assertSame('03', $result['forma_pago']);
        $this->assertSame('MXN', $result['moneda']);
        $this->assertSame(1000.0, $result['subtotal']);
        $this->assertSame(0.0, $result['descuento']);
        $this->assertSame(1160.0, $result['total']);
    }

    public function test_parses_emisor_and_receptor(): void
    {
        $result = $this->parser->parse($this->cfdiXml());

        $this->assertSame('ETE900101AAA', $result['rfc_emisor']);
        $this->assertSame('Empresa Test SA de CV', $result['nombre_emisor']);
        $this->assertSame('XAXX010101000', $result['rfc_receptor']);
        $this->assertSame('Publico en General', $result['nombre_receptor']);
        $this->assertSame('G03', $result['uso_cfdi']);
    }

    public function test_parses_uuid_from_timbre(): void
    {
        $result = $this->parser->parse($this->cfdiXml());

        $this->assertSame('6128e9b2-1234-5678-abcd-ef0123456789', $result['uuid']);
    }

    public function test_parses_concepto_principal(): void
    {
        $result = $this->parser->parse($this->cfdiXml());

        $this->assertSame('Servicios de consultoría', $result['concepto_principal']);
    }

    public function test_parses_iva_from_impuestos(): void
    {
        $result = $this->parser->parse($this->cfdiXml());

        $this->assertSame(160.0, $result['iva']);
    }

    public function test_normalizes_fecha_with_time_component(): void
    {
        $result = $this->parser->parse($this->cfdiXml(['Fecha' => '2024-12-01T23:59:59']));

        $this->assertSame('2024-12-01', $result['fecha_emision']);
    }

    public function test_rfc_is_uppercased(): void
    {
        $xml = str_replace('ETE900101AAA', 'ete900101aaa', $this->cfdiXml());
        $result = $this->parser->parse($xml);

        $this->assertSame('ETE900101AAA', $result['rfc_emisor']);
    }

    public function test_throws_on_invalid_xml(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/XML inválido/');

        $this->parser->parse('<esto no es xml válido>>>');
    }

    public function test_throws_when_rfc_missing(): void
    {
        $xml = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <cfdi:Comprobante
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            TipoDeComprobante="I" Fecha="2024-01-01T00:00:00"
            SubTotal="100" Total="116" Moneda="MXN">
          <cfdi:Emisor />
          <cfdi:Receptor />
          <cfdi:Conceptos />
        </cfdi:Comprobante>
        XML;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/RFC de emisor o receptor/');

        $this->parser->parse($xml);
    }

    public function test_serie_and_folio_nullable(): void
    {
        $result = $this->parser->parse($this->cfdiXml(['Serie' => '', 'Folio' => '']));

        $this->assertNull($result['serie']);
        $this->assertNull($result['folio']);
    }
}
