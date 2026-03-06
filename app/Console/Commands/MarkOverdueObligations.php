<?php

namespace App\Console\Commands;

use App\Services\FiscalObligationGenerator;
use Illuminate\Console\Command;

class MarkOverdueObligations extends Command
{
    protected $signature = 'app:mark-overdue-obligations';

    protected $description = 'Marca como vencidas todas las obligaciones fiscales pendientes cuya fecha límite ya pasó.';

    public function handle(FiscalObligationGenerator $generator): void
    {
        $count = $generator->markOverdue();

        $this->info("Obligaciones marcadas como vencidas: {$count}");
    }
}
