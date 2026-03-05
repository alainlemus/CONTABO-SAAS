<?php

namespace App\Services;

class XmlCfdiParser
{
    /**
     * Parsea un XML CFDI del SAT y retorna un array con los datos extraídos.
     *
     * @return array{
     *   uuid: string|null,
     *   serie: string|null,
     *   folio: string|null,
     *   fecha_emision: string,
     *   rfc_emisor: string,
     *   nombre_emisor: string|null,
     *   rfc_receptor: string,
     *   nombre_receptor: string|null,
     *   uso_cfdi: string|null,
     *   tipo_comprobante: string,
     *   metodo_pago: string|null,
     *   forma_pago: string|null,
     *   moneda: string,
     *   subtotal: float,
     *   descuento: float,
     *   iva: float,
     *   isr_retenido: float,
     *   iva_retenido: float,
     *   total: float,
     *   concepto_principal: string|null,
     * }
     *
     * @throws \RuntimeException si el XML es inválido o no es un CFDI
     */
    public function parse(string $xmlContent): array
    {
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            $errors = array_map(fn ($e) => trim($e->message), libxml_get_errors());
            libxml_clear_errors();

            throw new \RuntimeException('XML inválido: '.implode(', ', $errors));
        }

        $namespaces = $xml->getNamespaces(true);
        $cfdiNs = $namespaces['cfdi'] ?? '';

        $attrs = $xml->attributes();

        $tipoComprobante = (string) ($attrs['TipoDeComprobante'] ?? 'I');
        $serie = $this->nullableString($attrs['Serie'] ?? null);
        $folio = $this->nullableString($attrs['Folio'] ?? null);
        $fechaRaw = (string) ($attrs['Fecha'] ?? '');
        $metodoPago = $this->nullableString($attrs['MetodoPago'] ?? null);
        $formaPago = $this->nullableString($attrs['FormaPago'] ?? null);
        $moneda = (string) ($attrs['Moneda'] ?? 'MXN');
        $subtotal = (float) ($attrs['SubTotal'] ?? 0);
        $descuento = (float) ($attrs['Descuento'] ?? 0);
        $total = (float) ($attrs['Total'] ?? 0);

        // Emisor
        $emisor = $cfdiNs ? $xml->children($cfdiNs)->Emisor : $xml->Emisor;
        $rfcEmisor = (string) ($emisor->attributes()['Rfc'] ?? '');
        $nombreEmisor = $this->nullableString($emisor->attributes()['Nombre'] ?? null);

        // Receptor
        $receptor = $cfdiNs ? $xml->children($cfdiNs)->Receptor : $xml->Receptor;
        $rfcReceptor = (string) ($receptor->attributes()['Rfc'] ?? '');
        $nombreReceptor = $this->nullableString($receptor->attributes()['Nombre'] ?? null);
        $usoCfdi = $this->nullableString($receptor->attributes()['UsoCFDI'] ?? null);

        // Concepto principal (primero del listado)
        $conceptos = $cfdiNs ? $xml->children($cfdiNs)->Conceptos : $xml->Conceptos;
        $conceptoPrincipal = null;
        if ($conceptos) {
            $concepto = $cfdiNs ? $conceptos->children($cfdiNs)->Concepto : $conceptos->Concepto;
            if ($concepto) {
                $conceptoPrincipal = $this->nullableString($concepto->attributes()['Descripcion'] ?? null);
            }
        }

        // Impuestos
        $iva = 0.0;
        $isrRetenido = 0.0;
        $ivaRetenido = 0.0;

        $impuestos = $cfdiNs ? $xml->children($cfdiNs)->Impuestos : $xml->Impuestos;
        if ($impuestos) {
            $iva = (float) ($impuestos->attributes()['TotalImpuestosTrasladados'] ?? 0);

            $retenciones = $cfdiNs ? $impuestos->children($cfdiNs)->Retenciones : $impuestos->Retenciones;
            if ($retenciones) {
                $retencionNodes = $cfdiNs ? $retenciones->children($cfdiNs)->Retencion : $retenciones->Retencion;
                foreach ($retencionNodes as $ret) {
                    $impuesto = (string) ($ret->attributes()['Impuesto'] ?? '');
                    $importe = (float) ($ret->attributes()['Importe'] ?? 0);
                    if ($impuesto === '001') {
                        $isrRetenido += $importe;
                    } elseif ($impuesto === '002') {
                        $ivaRetenido += $importe;
                    }
                }
            }
        }

        // UUID del timbre fiscal digital
        $uuid = null;
        $timbreNs = $namespaces['tfd'] ?? ($namespaces['TimbreFiscalDigital'] ?? null);
        if ($timbreNs) {
            $timbre = $xml->children($timbreNs)->TimbreFiscalDigital ?? null;
            if ($timbre) {
                $uuid = $this->nullableString($timbre->attributes()['UUID'] ?? null);
            }
        }

        // Normalizar fecha (puede venir con hora: 2024-01-15T12:00:00)
        $fechaEmision = strlen($fechaRaw) >= 10 ? substr($fechaRaw, 0, 10) : now()->toDateString();

        if (empty($rfcEmisor) || empty($rfcReceptor)) {
            throw new \RuntimeException('El XML no contiene RFC de emisor o receptor. Verifica que sea un CFDI válido.');
        }

        return [
            'uuid' => $uuid,
            'serie' => $serie,
            'folio' => $folio,
            'fecha_emision' => $fechaEmision,
            'rfc_emisor' => strtoupper($rfcEmisor),
            'nombre_emisor' => $nombreEmisor,
            'rfc_receptor' => strtoupper($rfcReceptor),
            'nombre_receptor' => $nombreReceptor,
            'uso_cfdi' => $usoCfdi,
            'tipo_comprobante' => $tipoComprobante,
            'metodo_pago' => $metodoPago,
            'forma_pago' => $formaPago,
            'moneda' => $moneda,
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'iva' => $iva,
            'isr_retenido' => $isrRetenido,
            'iva_retenido' => $ivaRetenido,
            'total' => $total,
            'concepto_principal' => $conceptoPrincipal,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $str = trim((string) $value);

        return $str !== '' ? $str : null;
    }
}
