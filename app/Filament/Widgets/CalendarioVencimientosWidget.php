<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Models\FiscalObligation;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CalendarioVencimientosWidget extends BaseWidget
{
    protected static ?string $heading = 'Calendario de vencimientos (próximos 30 días)';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = now()->toDateString();
        $in30days = now()->addDays(30)->toDateString();

        return $table
            ->query(
                FiscalObligation::query()
                    ->whereIn('status', [ObligationStatus::Pending, ObligationStatus::Overdue])
                    ->whereBetween('due_date', [$today, $in30days])
                    ->orderBy('due_date', 'asc')
            )
            ->defaultGroup(
                Group::make('due_date')
                    ->label('Fecha de vencimiento')
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(
                        fn (FiscalObligation $record): string => $record->due_date->locale('es')->isoFormat('dddd D [de] MMMM YYYY')
                    )
                    ->collapsible()
                    ->orderQueryUsing(fn ($query, $direction) => $query->orderBy('due_date', $direction))
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Obligación')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge(),

                Tables\Columns\TextColumn::make('period_label')
                    ->label('Período')
                    ->getStateUsing(fn (FiscalObligation $record): string => $record->periodLabel()),

                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Días restantes')
                    ->getStateUsing(function (FiscalObligation $record): string {
                        $days = (int) Carbon::today()->diffInDays($record->due_date, false);

                        if ($days < 0) {
                            return 'Vencida';
                        }

                        if ($days === 0) {
                            return 'Hoy';
                        }

                        return $days === 1 ? '1 día' : "{$days} días";
                    })
                    ->badge()
                    ->color(function (FiscalObligation $record): string {
                        $days = (int) Carbon::today()->diffInDays($record->due_date, false);

                        if ($days <= 0) {
                            return 'danger';
                        }

                        if ($days <= 5) {
                            return 'warning';
                        }

                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estatus')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->color(fn ($state) => $state->color()),
            ])
            ->actions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (FiscalObligation $record): string => FiscalObligationResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Sin vencimientos en los próximos 30 días')
            ->emptyStateDescription('No hay obligaciones pendientes o vencidas en el período.');
    }
}
