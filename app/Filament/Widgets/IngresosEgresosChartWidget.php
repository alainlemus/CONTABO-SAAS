<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\Dashboard;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class IngresosEgresosChartWidget extends ChartWidget
{
    protected ?string $heading = 'Ingresos vs Egresos (últimos 6 meses)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $userId = auth()->id();
        $clientId = Dashboard::getActiveClientId();

        $months = collect(range(5, 0))->map(fn (int $ago): Carbon => Carbon::now()->subMonths($ago)->startOfMonth());

        $invoiceQuery = fn (string $type): \Illuminate\Database\Eloquent\Builder => Invoice::query()
            ->when($clientId,
                fn ($q) => $q->where('client_id', $clientId),
                fn ($q) => $q->whereHas('client', fn ($c) => $c->where('user_id', $userId))
            )
            ->where('type', $type);

        $ingresos = $months->map(fn (Carbon $month): float => (float) $invoiceQuery('ingreso')
            ->whereYear('fecha_emision', $month->year)
            ->whereMonth('fecha_emision', $month->month)
            ->sum('total')
        )->values()->all();

        $egresos = $months->map(fn (Carbon $month): float => (float) $invoiceQuery('gasto')
            ->whereYear('fecha_emision', $month->year)
            ->whereMonth('fecha_emision', $month->month)
            ->sum('total')
        )->values()->all();

        $labels = $months->map(fn (Carbon $m): string => $m->locale('es')->isoFormat('MMM YYYY'))->all();

        return [
            'datasets' => [
                [
                    'label' => 'Ingresos',
                    'data' => $ingresos,
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Egresos',
                    'data' => $egresos,
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
