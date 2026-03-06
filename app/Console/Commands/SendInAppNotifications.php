<?php

namespace App\Console\Commands;

use App\Enums\ObligationStatus;
use App\Models\FiscalObligation;
use App\Models\User;
use Filament\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class SendInAppNotifications extends Command
{
    protected $signature = 'app:send-inapp-notifications
                            {--due-soon-days=3 : Días de anticipación para avisar sobre obligaciones próximas a vencer}';

    protected $description = 'Envía notificaciones in-app en el panel Filament a admins con obligaciones vencidas o próximas a vencer.';

    public function handle(): void
    {
        $dueSoonDays = (int) $this->option('due-soon-days');
        $today = now()->toDateString();
        $targetDate = now()->addDays($dueSoonDays)->toDateString();

        $admins = User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->get();

        $totalNotified = 0;

        foreach ($admins as $admin) {
            $overdueCount = FiscalObligation::withoutGlobalScopes()
                ->whereHas('client', fn ($q) => $q->withoutGlobalScopes()->where('user_id', $admin->id))
                ->where('status', ObligationStatus::Overdue)
                ->count();

            $dueSoonCount = FiscalObligation::withoutGlobalScopes()
                ->whereHas('client', fn ($q) => $q->withoutGlobalScopes()->where('user_id', $admin->id))
                ->where('status', ObligationStatus::Pending)
                ->whereDate('due_date', '>=', $today)
                ->whereDate('due_date', '<=', $targetDate)
                ->count();

            if ($overdueCount > 0) {
                Notification::make()
                    ->title('Obligaciones fiscales vencidas')
                    ->body("Tienes {$overdueCount} obligación(es) fiscal(es) vencida(s) sin presentar.")
                    ->danger()
                    ->actions([
                        NotificationAction::make('ver')
                            ->label('Ver obligaciones')
                            ->url(route('filament.admin.resources.fiscal-obligations.index'))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);

                $totalNotified++;
            }

            if ($dueSoonCount > 0) {
                Notification::make()
                    ->title('Obligaciones próximas a vencer')
                    ->body("Tienes {$dueSoonCount} obligación(es) que vencen en los próximos {$dueSoonDays} día(s).")
                    ->warning()
                    ->actions([
                        NotificationAction::make('ver')
                            ->label('Ver obligaciones')
                            ->url(route('filament.admin.resources.fiscal-obligations.index'))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);

                $totalNotified++;
            }
        }

        $this->info("Notificaciones in-app enviadas: {$totalNotified}");
    }
}
