<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Pages\Dashboard;
use App\Models\FiscalObligation;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ObligacionesPorEstatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Obligaciones por estatus (últimos 6 meses)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $ownerId = auth()->user()->ownerId();
        $clientId = Dashboard::getActiveClientId();

        $months = collect(range(5, 0))->map(function (int $monthsAgo): Carbon {
            return Carbon::now()->subMonths($monthsAgo)->startOfMonth();
        });

        $statuses = [
            ObligationStatus::Pending,
            ObligationStatus::Overdue,
            ObligationStatus::Presented,
            ObligationStatus::NotApplicable,
        ];

        $colors = [
            ObligationStatus::Pending->value => 'rgba(251, 191, 36, 0.8)',
            ObligationStatus::Overdue->value => 'rgba(239, 68, 68, 0.8)',
            ObligationStatus::Presented->value => 'rgba(34, 197, 94, 0.8)',
            ObligationStatus::NotApplicable->value => 'rgba(156, 163, 175, 0.8)',
        ];

        $datasets = collect($statuses)->map(function (ObligationStatus $status) use ($months, $colors, $ownerId, $clientId): array {
            $dateColumn = $status === ObligationStatus::Presented ? 'presented_at' : 'due_date';

            $data = $months->map(function (Carbon $month) use ($status, $dateColumn, $ownerId, $clientId): int {
                return FiscalObligation::query()
                    ->where('status', $status)
                    ->when($clientId,
                        fn ($q) => $q->where('client_id', $clientId),
                        fn ($q) => $q->whereHas('client', fn ($c) => $c->where('user_id', $ownerId))
                    )
                    ->whereYear($dateColumn, $month->year)
                    ->whereMonth($dateColumn, $month->month)
                    ->count();
            })->values()->all();

            return [
                'label' => $status->label(),
                'data' => $data,
                'backgroundColor' => $colors[$status->value],
            ];
        })->values()->all();

        $labels = $months->map(fn (Carbon $m): string => $m->locale('es')->isoFormat('MMM YYYY'))->all();

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
