<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
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
        $now = Carbon::now();
        $in7days = $now->copy()->addDays(7);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $overdueCount = FiscalObligation::query()
            ->where('status', ObligationStatus::Overdue)
            ->count();

        $dueSoonCount = FiscalObligation::query()
            ->where('status', ObligationStatus::Pending)
            ->whereBetween('due_date', [$now->toDateString(), $in7days->toDateString()])
            ->count();

        $presentedThisMonth = FiscalObligation::query()
            ->where('status', ObligationStatus::Presented)
            ->whereBetween('presented_at', [$startOfMonth, $endOfMonth])
            ->count();

        $activeClients = Client::query()
            ->where('status', 'active')
            ->count();

        $invoicesThisMonth = Invoice::query()
            ->whereHas('client', fn ($q) => $q->where('user_id', $ownerId))
            ->whereBetween('fecha_emision', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->count();

        $totalBilledThisMonth = Invoice::query()
            ->whereHas('client', fn ($q) => $q->where('user_id', $ownerId))
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
