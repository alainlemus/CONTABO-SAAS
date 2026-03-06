<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\FiscalObligationGenerator;
use Illuminate\Console\Command;

class GenerateAnnualObligations extends Command
{
    protected $signature = 'app:generate-annual-obligations
                            {--year= : Año fiscal a generar (default: año anterior)}';

    protected $description = 'Genera las obligaciones fiscales anuales para todos los clientes activos con régimen fiscal definido.';

    public function handle(FiscalObligationGenerator $generator): void
    {
        // Por defecto genera para el año ANTERIOR (las declaraciones anuales
        // del ejercicio X vencen en marzo/abril del año X+1).
        $year = (int) ($this->option('year') ?: now()->subYear()->year);

        $clients = Client::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('tax_regime')
            ->get();

        $total = 0;

        foreach ($clients as $client) {
            $generated = $generator->generateYearForClient($client, $year);
            $total += $generated->count();
        }

        $this->info("Clientes procesados: {$clients->count()} — Obligaciones anuales generadas: {$total}");
    }
}
