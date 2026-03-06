<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\FiscalObligation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FiscalObligationsRelationManager extends RelationManager
{
    protected static string $relationship = 'fiscalObligations';

    protected static ?string $title = 'Obligaciones fiscales';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->defaultSort('due_date', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (ObligationType $state): string => $state->label())
                    ->color('primary'),

                TextColumn::make('period_label')
                    ->label('Período')
                    ->state(fn (FiscalObligation $record): string => $record->periodLabel())
                    ->sortable(false),

                TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn (FiscalObligation $record): string => $record->isOverdue() ? 'danger' : 'gray'),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->formatStateUsing(fn (ObligationStatus $state): string => $state->label())
                    ->color(fn (ObligationStatus $state): string => $state->color()),

                TextColumn::make('presented_at')
                    ->label('Presentada')
                    ->date('d/m/Y')
                    ->placeholder('—'),

                TextColumn::make('reference')
                    ->label('Referencia')
                    ->limit(30)
                    ->placeholder('—'),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
