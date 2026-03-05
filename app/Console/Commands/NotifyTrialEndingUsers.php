<?php

namespace App\Console\Commands;

use App\Jobs\SendTrialEndingEmail;
use App\Models\User;
use Illuminate\Console\Command;

class NotifyTrialEndingUsers extends Command
{
    protected $signature = 'app:notify-trial-ending-users';

    protected $description = 'Envía un email a los admins cuyo trial vence en 2 días.';

    public function handle(): void
    {
        $users = User::query()
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now()->addDays(2)->startOfDay(), now()->addDays(2)->endOfDay()])
            ->get();

        foreach ($users as $user) {
            SendTrialEndingEmail::dispatch($user);
        }

        $this->info("Notificaciones enviadas: {$users->count()}");
    }
}
