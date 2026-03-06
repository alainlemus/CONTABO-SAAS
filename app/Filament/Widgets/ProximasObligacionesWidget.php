<?php

namespace App\Filament\Widgets;

use App\Enums\ObligationStatus;
use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use App\Models\FiscalObligation;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasObligacionesWidget extends BaseWidget
{
    protected static ?string $heading = 'Próximas obligaciones por vencer';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FiscalObligation::query()
                    ->whereIn('status', [ObligationStatus::Pending, ObligationStatus::Overdue])
                    ->where('due_date', '>=', now()->toDateString())
                    ->orderBy('due_date', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge(),

                Tables\Columns\TextColumn::make('period_label')
                    ->label('Período')
                    ->getStateUsing(fn (FiscalObligation $record): string => $record->periodLabel()),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->sortable(),

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
            ->paginated(false);
    }
}
