<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firm_id' => \App\Models\Firm::factory(),
            'code' => fake()->unique()->numerify('1###-##'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['asset', 'liability', 'equity', 'income', 'expense']),
            'category' => fake()->randomElement(['cash', 'bank', 'receivable', 'payable', 'revenue', 'cost']),
            'currency' => 'MXN',
            'level' => fake()->numberBetween(1, 5),
            'is_major' => fake()->boolean(20),
            'is_active' => true,
        ];
    }
}
