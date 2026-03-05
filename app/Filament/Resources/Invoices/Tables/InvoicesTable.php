<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
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

                IconColumn::make('xml_path')
                    ->label('XML')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->state(fn (Invoice $record): bool => (bool) $record->xml_path)
                    ->toggleable(),

                IconColumn::make('pdf_path')
                    ->label('PDF')
                    ->boolean()
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('danger')
                    ->falseColor('gray')
                    ->state(fn (Invoice $record): bool => (bool) $record->pdf_path)
                    ->toggleable(),
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
            ->actions([
                ActionGroup::make([
                    Action::make('download_xml')
                        ->label('Descargar XML')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->url(fn (Invoice $record): string => route('invoices.download.xml', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Invoice $record): bool => (bool) $record->xml_path),

                    Action::make('download_pdf')
                        ->label('Descargar PDF')
                        ->icon('heroicon-o-document')
                        ->color('danger')
                        ->url(fn (Invoice $record): string => route('invoices.download.pdf', $record))
                        ->openUrlInNewTab()
                        ->visible(fn (Invoice $record): bool => (bool) $record->pdf_path),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
