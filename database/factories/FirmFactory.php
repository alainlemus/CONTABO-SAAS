<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Firm>
 */
class FirmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Contable',
            'tax_id' => 'F'.fake()->unique()->numerify('##########'),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'billing_plan' => fake()->randomElement(['starter', 'growth', 'scale']),
            'billing_expires_at' => fake()->dateTimeBetween('now', '+6 months'),
            'settings' => [
                'timezone' => 'America/Mexico_City',
                'decimal_format' => '0,000.00',
            ],
        ];
    }
}
