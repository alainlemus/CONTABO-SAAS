<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'uuid',
        'serie',
        'folio',
        'fecha_emision',
        'rfc_emisor',
        'nombre_emisor',
        'rfc_receptor',
        'nombre_receptor',
        'uso_cfdi',
        'tipo_comprobante',
        'metodo_pago',
        'forma_pago',
        'moneda',
        'subtotal',
        'descuento',
        'iva',
        'isr_retenido',
        'iva_retenido',
        'total',
        'concepto_principal',
        'xml_path',
        'pdf_path',
        'status',
        'parse_error',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'iva' => 'decimal:2',
            'isr_retenido' => 'decimal:2',
            'iva_retenido' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
