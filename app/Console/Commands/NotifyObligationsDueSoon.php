<?php

namespace App\Console\Commands;

use App\Enums\ObligationStatus;
use App\Jobs\SendObligationDueSoonEmail;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyObligationsDueSoon extends Command
{
    protected $signature = 'app:notify-obligations-due-soon
                            {--days=3 : Días de anticipación para avisar (default: 3)}';

    protected $description = 'Avisa a cada admin sobre sus obligaciones fiscales pendientes que vencen en N días.';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days)->toDateString();

        // Agrupamos por admin (owner_id del cliente) para enviar un solo email por despacho
        $obligationsByAdmin = FiscalObligation::withoutGlobalScopes()
            ->with(['client' => fn ($q) => $q->withoutGlobalScopes()])
            ->where('status', ObligationStatus::Pending)
            ->whereDate('due_date', $targetDate)
            ->get()
            ->groupBy(fn (FiscalObligation $o) => $o->client->user_id);

        $notified = 0;

        foreach ($obligationsByAdmin as $userId => $obligations) {
            $admin = User::find($userId);

            if (! $admin) {
                continue;
            }

            SendObligationDueSoonEmail::dispatch($admin, $obligations, $days);
            $notified++;
        }

        $this->info("Admins notificados: {$notified} — Obligaciones incluidas: ".$obligationsByAdmin->flatten()->count());
    }
}
