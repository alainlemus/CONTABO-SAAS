<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Services\FiscalObligationGenerator;
use Illuminate\Console\Command;

class GenerateMonthlyObligations extends Command
{
    protected $signature = 'app:generate-monthly-obligations
                            {--year= : Año del período (default: mes anterior)}
                            {--month= : Mes del período (default: mes anterior)}';

    protected $description = 'Genera las obligaciones fiscales del mes para todos los clientes activos con régimen fiscal definido.';

    public function handle(FiscalObligationGenerator $generator): void
    {
        // Por defecto genera para el mes ANTERIOR (las obligaciones se generan
        // al inicio del mes siguiente al período, antes de su vencimiento el día 17).
        $year = (int) ($this->option('year') ?: now()->subMonth()->year);
        $month = (int) ($this->option('month') ?: now()->subMonth()->month);

        $clients = Client::withoutGlobalScopes()
            ->where('status', 'active')
            ->whereNotNull('tax_regime')
            ->get();

        $total = 0;

        foreach ($clients as $client) {
            $generated = $generator->generateForClient($client, $year, $month);
            $total += $generated->count();
        }

        $this->info("Clientes procesados: {$clients->count()} — Obligaciones generadas: {$total}");
    }
}
