<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(?User $admin = null): void
    {
        $admin ??= User::where('email', 'admin@contabo.test')->firstOrFail();

        $clients = Client::withoutGlobalScopes()->where('user_id', $admin->id)->get();

        if ($clients->isEmpty()) {
            return;
        }

        $invoiceTemplates = [
            [
                'tipo_comprobante' => 'I',
                'metodo_pago' => 'PPD',
                'forma_pago' => '99',
                'moneda' => 'MXN',
                'uso_cfdi' => 'G03',
                'concepto_principal' => 'Servicios de contabilidad mensual',
                'subtotal' => 8620.69,
                'iva' => 1379.31,
                'total' => 10000.00,
            ],
            [
                'tipo_comprobante' => 'I',
                'metodo_pago' => 'PUE',
                'forma_pago' => '03',
                'moneda' => 'MXN',
                'uso_cfdi' => 'G01',
                'concepto_principal' => 'Honorarios por asesoría fiscal',
                'subtotal' => 17241.38,
                'iva' => 2758.62,
                'total' => 20000.00,
            ],
            [
                'tipo_comprobante' => 'I',
                'metodo_pago' => 'PUE',
                'forma_pago' => '01',
                'moneda' => 'MXN',
                'uso_cfdi' => 'G03',
                'concepto_principal' => 'Declaración anual persona moral',
                'subtotal' => 5172.41,
                'iva' => 827.59,
                'total' => 6000.00,
            ],
            [
                'tipo_comprobante' => 'E',
                'metodo_pago' => 'PUE',
                'forma_pago' => '01',
                'moneda' => 'MXN',
                'uso_cfdi' => 'G01',
                'concepto_principal' => 'Nota de crédito por ajuste de honorarios',
                'subtotal' => -1724.14,
                'iva' => -275.86,
                'total' => -2000.00,
            ],
        ];

        $statuses = ['procesado', 'procesado', 'procesado', 'error'];

        foreach ($clients->take(5) as $client) {
            $baseMonth = now()->subMonths(3);

            foreach ($invoiceTemplates as $index => $template) {
                $emisionDate = $baseMonth->copy()->addMonths($index % 3)->startOfMonth()->addDays(rand(1, 20));

                Invoice::create(array_merge($template, [
                    'client_id' => $client->id,
                    'type' => 'ingreso',
                    'uuid' => Str::uuid()->toString(),
                    'serie' => 'A',
                    'folio' => str_pad((string) rand(1, 999), 4, '0', STR_PAD_LEFT),
                    'fecha_emision' => $emisionDate,
                    'rfc_emisor' => $client->tax_id,
                    'nombre_emisor' => $client->name,
                    'rfc_receptor' => 'XAXX010101000',
                    'nombre_receptor' => 'Público en General',
                    'status' => $statuses[$index] ?? 'procesado',
                    'descuento' => 0,
                    'isr_retenido' => 0,
                    'iva_retenido' => 0,
                ]));
            }
        }
    }
}
