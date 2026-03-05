<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha_emision')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'ingreso' => 'success',
                        'gasto' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'ingreso' => 'Ingreso',
                        'gasto' => 'Gasto',
                        default => $state,
                    }),

                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('uuid')
                    ->label('UUID')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->uuid)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rfc_emisor')
                    ->label('RFC Emisor')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('nombre_emisor')
                    ->label('Emisor')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),

                TextColumn::make('concepto_principal')
                    ->label('Concepto')
                    ->limit(35)
                    ->toggleable(),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('MXN')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('iva')
                    ->label('IVA')
                    ->money('MXN')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('MXN')
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'procesado' => 'success',
                        'pendiente' => 'warning',
                        'error' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'procesado' => 'Procesado',
                        'pendiente' => 'Pendiente',
                        'error' => 'Error',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'ingreso' => 'Ingresos',
                        'gasto' => 'Gastos',
                    ]),

                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options([
                        'procesado' => 'Procesado',
                        'pendiente' => 'Pendiente',
                        'error' => 'Error',
                    ]),

                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('fecha_emision', 'desc')
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
