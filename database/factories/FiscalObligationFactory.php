<?php

namespace Database\Factories;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FiscalObligation>
 */
class FiscalObligationFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(ObligationType::cases());
        $year = fake()->numberBetween(2023, 2025);
        $isAnnual = $type->periodicity() === 'anual';
        $month = $isAnnual ? null : fake()->numberBetween(1, 12);

        $dueDate = $isAnnual
            ? now()->setYear($year + 1)->setMonth($type === ObligationType::DeclaracionAnualPf ? 4 : 3)->setDay($type->dueDayOfNextMonth())->toDateString()
            : now()->setYear($year)->setMonth($month)->addMonthNoOverflow()->setDay(17)->toDateString();

        return [
            'client_id' => Client::factory(),
            'type' => $type,
            'period_year' => $year,
            'period_month' => $month,
            'due_date' => $dueDate,
            'status' => ObligationStatus::Pending,
            'presented_at' => null,
            'reference' => null,
            'notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => ObligationStatus::Pending]);
    }

    public function presented(): static
    {
        return $this->state([
            'status' => ObligationStatus::Presented,
            'presented_at' => now()->subDays(fake()->numberBetween(1, 30)),
            'reference' => fake()->numerify('REF-#####'),
        ]);
    }

    public function overdue(): static
    {
        return $this->state([
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(fake()->numberBetween(1, 60)),
        ]);
    }

    public function notApplicable(): static
    {
        return $this->state(['status' => ObligationStatus::NotApplicable]);
    }

    public function forType(ObligationType $type): static
    {
        return $this->state(['type' => $type]);
    }

    public function withAcuse(): static
    {
        return $this->state(['acuse_pdf_path' => 'fiscal-obligations/acuses/1/acuse_sat.pdf']);
    }
}
