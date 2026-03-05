<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre / Razón social')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('tax_id')
                    ->label('RFC')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('person_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fisica' => 'info',
                        'moral' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fisica' => 'Física',
                        'moral' => 'Moral',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'onboarding' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                        'onboarding' => 'En alta',
                        default => $state,
                    }),
                TextColumn::make('compliance_level')
                    ->label('Cumplimiento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'warning',
                        'low' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high' => 'Alto',
                        'medium' => 'Medio',
                        'low' => 'Bajo',
                        default => $state,
                    }),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('billing_cycle')
                    ->label('Facturación')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Mensual',
                        'quarterly' => 'Trimestral',
                        'annual' => 'Anual',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('relationship_started_at')
                    ->label('Inicio relación')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('efirma_cer_path')
                    ->label('CER')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->state(fn (Client $record): bool => (bool) $record->efirma_cer_path)
                    ->toggleable(),

                IconColumn::make('efirma_key_path')
                    ->label('KEY')
                    ->boolean()
                    ->trueIcon('heroicon-o-key')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->state(fn (Client $record): bool => (bool) $record->efirma_key_path)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estatus')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                        'onboarding' => 'En alta',
                    ]),
                SelectFilter::make('person_type')
                    ->label('Tipo de persona')
                    ->options([
                        'fisica' => 'Persona Física',
                        'moral' => 'Persona Moral',
                    ]),
                SelectFilter::make('compliance_level')
                    ->label('Cumplimiento')
                    ->options([
                        'high' => 'Alto',
                        'medium' => 'Medio',
                        'low' => 'Bajo',
                    ]),
                SelectFilter::make('billing_cycle')
                    ->label('Ciclo de facturación')
                    ->options([
                        'monthly' => 'Mensual',
                        'quarterly' => 'Trimestral',
                        'annual' => 'Anual',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                ActionGroup::make([
                    Action::make('download_cer')
                        ->label('Descargar CER')
                        ->icon('heroicon-o-shield-check')
                        ->color('success')
                        ->url(fn (Client $record): string => route('clients.download.cer', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Client $record): bool => (bool) $record->efirma_cer_path && auth()->user()?->isAdmin()),

                    Action::make('download_key')
                        ->label('Descargar KEY')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->url(fn (Client $record): string => route('clients.download.key', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Client $record): bool => (bool) $record->efirma_key_path && auth()->user()?->isAdmin()),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
