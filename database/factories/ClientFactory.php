<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $personType = fake()->randomElement(['fisica', 'moral']);

        return [
            'name' => fake()->company(),
            'tax_id' => 'C'.fake()->unique()->numerify('###########'),
            'person_type' => $personType,
            'tax_regime' => fake()->randomElement(['601', '612', '621', '625', '626']),
            'curp' => $personType === 'fisica' ? strtoupper(fake()->bothify('????######????##??')) : null,
            'legal_rep_name' => $personType === 'moral' ? fake()->name() : null,
            'legal_rep_rfc' => $personType === 'moral' ? strtoupper(fake()->bothify('????######???')) : null,
            'economic_activity' => fake()->bs(),
            'scian_code' => fake()->numerify('#####'),
            'employee_count' => fake()->optional()->numberBetween(1, 200),
            'relationship_started_at' => fake()->optional()->dateTimeBetween('-3 years', 'now'),
            'obligations_periodicity' => fake()->randomElement(['mensual', 'bimestral', 'anual']),
            'email' => fake()->companyEmail(),
            'phone' => null,
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'MX',
            'postal_code' => fake()->postcode(),
            'status' => fake()->randomElement(['active', 'inactive', 'onboarding']),
            'billing_cycle' => fake()->randomElement(['monthly', 'quarterly', 'annual']),
            'onboarding_notes' => fake()->sentence(),
            'onboarding_completed_at' => fake()->optional()->dateTimeBetween('-2 months', 'now'),
            'portal_sat_user' => fake()->userName(),
            'portal_sat_password' => fake()->password(10, 16),
            'efirma_cer_path' => null,
            'efirma_key_path' => null,
            'documents' => [
                'constancia_situacion_fiscal' => fake()->boolean(),
                'poder_notarial' => fake()->boolean(),
                'identificacion' => fake()->boolean(),
            ],
            'compliance_level' => fake()->randomElement(['high', 'medium', 'low']),
        ];
    }
}
