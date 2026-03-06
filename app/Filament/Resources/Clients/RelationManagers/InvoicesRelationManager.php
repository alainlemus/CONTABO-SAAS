<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Models\Invoice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Facturas';

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
            ->recordTitleAttribute('uuid')
            ->defaultSort('fecha_emision', 'desc')
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->limit(18)
                    ->tooltip(fn (Invoice $record): string => $record->uuid ?? '')
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('tipo_comprobante')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'I' => 'success',
                        'E' => 'danger',
                        'P' => 'info',
                        'N' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'I' => 'Ingreso',
                        'E' => 'Egreso',
                        'P' => 'Pago',
                        'N' => 'Nómina',
                        default => $state ?? '—',
                    }),

                TextColumn::make('fecha_emision')
                    ->label('Fecha emisión')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('nombre_receptor')
                    ->label('Receptor')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'vigente' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'vigente' => 'Vigente',
                        'cancelado' => 'Cancelado',
                        default => $state ?? '—',
                    }),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
