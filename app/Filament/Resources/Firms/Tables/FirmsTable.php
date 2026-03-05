<?php

namespace App\Filament\Resources\Firms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FirmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Despacho')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('tax_id')
                    ->label('RFC')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('billing_plan')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'starter' => 'gray',
                        'growth'  => 'info',
                        'scale'   => 'success',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('contact_email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('contact_phone')
                    ->label('Teléfono')
                    ->toggleable(),
                TextColumn::make('billing_expires_at')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('clients_count')
                    ->label('Clientes')
                    ->counts('clients')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('billing_plan')
                    ->label('Plan')
                    ->options([
                        'starter' => 'Starter',
                        'growth'  => 'Growth',
                        'scale'   => 'Scale',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
