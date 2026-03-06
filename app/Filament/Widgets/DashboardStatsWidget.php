<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Pages\Dashboard;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ownerId = auth()->user()->ownerId();
        $clientId = Dashboard::getActiveClientId();
        $now = Carbon::now();
        $in7days = $now->copy()->addDays(7);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $obligationQuery = fn () => FiscalObligation::query()
            ->when($clientId,
                fn ($q) => $q->where('client_id', $clientId),
                fn ($q) => $q->whereHas('client', fn ($c) => $c->where('user_id', $ownerId))
            );

        $overdueCount = $obligationQuery()
            ->where('status', ObligationStatus::Overdue)
            ->count();

        $dueSoonCount = $obligationQuery()
            ->where('status', ObligationStatus::Pending)
            ->whereBetween('due_date', [$now->toDateString(), $in7days->toDateString()])
            ->count();

        $presentedThisMonth = $obligationQuery()
            ->where('status', ObligationStatus::Presented)
            ->whereBetween('presented_at', [$startOfMonth, $endOfMonth])
            ->count();

        $activeClients = Client::query()
            ->when($clientId,
                fn ($q) => $q->where('id', $clientId),
                fn ($q) => $q->where('user_id', $ownerId)
            )
            ->where('status', 'active')
            ->count();

        $invoiceQuery = fn () => Invoice::query()
            ->when($clientId,
                fn ($q) => $q->where('client_id', $clientId),
                fn ($q) => $q->whereHas('client', fn ($c) => $c->where('user_id', $ownerId))
            );

        $invoicesThisMonth = $invoiceQuery()
            ->whereBetween('fecha_emision', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->count();

        $totalBilledThisMonth = $invoiceQuery()
            ->whereBetween('fecha_emision', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('total');

        return [
            Stat::make('Obligaciones vencidas', $overdueCount)
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Por vencer en 7 días', $dueSoonCount)
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Presentadas este mes', $presentedThisMonth)
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Clientes activos', $activeClients)
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('Facturas del mes', $invoicesThisMonth)
                ->color('gray')
                ->icon('heroicon-o-document-text'),

            Stat::make('Total facturado', '$'.number_format((float) $totalBilledThisMonth, 2))
                ->color('gray')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
