<?php

namespace Database\Seeders;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Database\Seeder;

class FiscalObligationSeeder extends Seeder
{
    public function run(?User $admin = null): void
    {
        $admin ??= User::where('email', 'admin@contabo.test')->firstOrFail();

        $clients = Client::withoutGlobalScopes()->where('user_id', $admin->id)->get();

        if ($clients->isEmpty()) {
            return;
        }

        $now = now();
        $currentYear = (int) $now->format('Y');
        $currentMonth = (int) $now->format('n');

        foreach ($clients as $client) {
            $obligations = ObligationType::forRegime($client->tax_regime);

            if (empty($obligations)) {
                continue;
            }

            // Generate obligations for the last 3 months + current month
            for ($monthsAgo = 3; $monthsAgo >= 0; $monthsAgo--) {
                $period = $now->copy()->subMonths($monthsAgo);
                $periodYear = (int) $period->format('Y');
                $periodMonth = (int) $period->format('n');

                foreach ($obligations as $type) {
                    if (! in_array($type->periodicity(), ['mensual', 'bimestral'])) {
                        continue; // skip annual for now; added separately below
                    }

                    if ($type->periodicity() === 'bimestral' && $periodMonth % 2 === 0) {
                        continue; // bimestral: only odd months (1, 3, 5, 7, 9, 11)
                    }

                    $dueDate = $period->copy()->addMonth()->day($type->dueDayOfNextMonth());

                    // Assign status based on whether due date is past
                    if ($monthsAgo >= 2) {
                        $status = ObligationStatus::Presented;
                        $presentedAt = $dueDate->copy()->subDays(rand(1, 5));
                    } elseif ($monthsAgo === 1) {
                        // Mix: some presented, some pending
                        $status = ($client->compliance_level === 'high')
                            ? ObligationStatus::Presented
                            : ObligationStatus::Pending;
                        $presentedAt = ($status === ObligationStatus::Presented)
                            ? $dueDate->copy()->subDays(rand(1, 3))
                            : null;
                    } else {
                        // Current month — always pending
                        $status = ObligationStatus::Pending;
                        $presentedAt = null;
                    }

                    FiscalObligation::create([
                        'client_id' => $client->id,
                        'type' => $type,
                        'period_year' => $periodYear,
                        'period_month' => $periodMonth,
                        'due_date' => $dueDate,
                        'status' => $status,
                        'presented_at' => $presentedAt,
                        'reference' => $status === ObligationStatus::Presented
                            ? strtoupper('REF-'.substr(md5(uniqid()), 0, 8))
                            : null,
                        'notes' => null,
                        'acuse_pdf_path' => null,
                    ]);
                }
            }

            // Add one annual obligation for current year
            $annualTypes = array_filter(
                $obligations,
                fn (ObligationType $t) => $t->periodicity() === 'anual'
            );

            foreach ($annualTypes as $type) {
                FiscalObligation::create([
                    'client_id' => $client->id,
                    'type' => $type,
                    'period_year' => $currentYear,
                    'period_month' => null,
                    'due_date' => now()->setMonth($type === ObligationType::DeclaracionAnualPm ? 3 : 4)
                        ->day($type->dueDayOfNextMonth())
                        ->setYear($currentYear + 1),
                    'status' => ObligationStatus::Pending,
                    'presented_at' => null,
                    'reference' => null,
                    'notes' => null,
                    'acuse_pdf_path' => null,
                ]);
            }
        }
    }
}
