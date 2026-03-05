<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['ingreso', 'gasto']);
        $subtotal = fake()->randomFloat(2, 100, 50000);
        $iva = round($subtotal * 0.16, 2);
        $total = $subtotal + $iva;

        return [
            'client_id' => Client::factory(),
            'type' => $type,
            'uuid' => fake()->uuid(),
            'serie' => fake()->optional()->randomLetter(),
            'folio' => fake()->numerify('####'),
            'fecha_emision' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'rfc_emisor' => strtoupper(fake()->bothify('???######???')),
            'nombre_emisor' => fake()->company(),
            'rfc_receptor' => strtoupper(fake()->bothify('???######???')),
            'nombre_receptor' => fake()->company(),
            'uso_cfdi' => fake()->randomElement(['G01', 'G03', 'P01']),
            'tipo_comprobante' => 'I',
            'metodo_pago' => fake()->randomElement(['PUE', 'PPD']),
            'forma_pago' => fake()->randomElement(['03', '04', '28']),
            'moneda' => 'MXN',
            'subtotal' => $subtotal,
            'descuento' => 0,
            'iva' => $iva,
            'isr_retenido' => 0,
            'iva_retenido' => 0,
            'total' => $total,
            'concepto_principal' => fake()->bs(),
            'xml_path' => 'invoices/xml/fake-'.fake()->uuid().'.xml',
            'pdf_path' => null,
            'status' => 'procesado',
            'parse_error' => null,
        ];
    }

    public function ingreso(): static
    {
        return $this->state(['type' => 'ingreso']);
    }

    public function gasto(): static
    {
        return $this->state(['type' => 'gasto']);
    }

    public function conError(): static
    {
        return $this->state([
            'status' => 'error',
            'parse_error' => 'El XML no contiene RFC de emisor o receptor.',
        ]);
    }
}
