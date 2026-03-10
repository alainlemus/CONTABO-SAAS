<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ClientesEnRiesgoWidget extends BaseWidget
{
    protected static ?string $heading = 'Clientes en riesgo';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $clientId = Dashboard::getActiveClientId();

        return $table
            ->query(
                Client::query()
                    ->whereHas('fiscalObligations', fn ($q) => $q->where('status', ObligationStatus::Overdue))
                    ->withCount([
                        'fiscalObligations as overdue_count' => fn ($q) => $q->where('status', ObligationStatus::Overdue),
                    ])
                    ->withMin(
                        ['fiscalObligations as oldest_overdue_date' => fn ($q) => $q->where('status', ObligationStatus::Overdue)],
                        'due_date'
                    )
                    ->when($clientId, fn ($q) => $q->where('id', $clientId))
                    ->orderByDesc('overdue_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('tax_id')
                    ->label('RFC')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('RFC copiado'),

                Tables\Columns\TextColumn::make('overdue_count')
                    ->label('Vencidas')
                    ->badge()
                    ->color('danger')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('oldest_overdue_days')
                    ->label('Mayor retraso')
                    ->getStateUsing(function (Client $record): string {
                        if (! $record->oldest_overdue_date) {
                            return '—';
                        }

                        $days = (int) Carbon::parse($record->oldest_overdue_date)->diffInDays(Carbon::today());

                        return $days === 1 ? '1 día' : "{$days} días";
                    })
                    ->badge()
                    ->color(function (Client $record): string {
                        if (! $record->oldest_overdue_date) {
                            return 'gray';
                        }

                        $days = (int) Carbon::parse($record->oldest_overdue_date)->diffInDays(Carbon::today());

                        if ($days >= 30) {
                            return 'danger';
                        }

                        if ($days >= 7) {
                            return 'warning';
                        }

                        return 'info';
                    }),

                Tables\Columns\TextColumn::make('compliance_level')
                    ->label('Cumplimiento')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'high' => 'Alto',
                        'low' => 'Bajo',
                        default => 'Medio',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'high' => 'success',
                        'low' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver cliente')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Client $record): string => ClientResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Sin clientes en riesgo')
            ->emptyStateDescription('Todos los clientes están al corriente con sus obligaciones fiscales.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}
