<?php

namespace App\Filament\Exports;

use App\Models\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fecha_emision')
                ->label('Fecha de emisión')
                ->formatStateUsing(fn ($state): string => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : ''),

            ExportColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'ingreso' => 'Ingreso',
                    'gasto' => 'Gasto',
                    default => $state,
                }),

            ExportColumn::make('client.name')
                ->label('Cliente'),

            ExportColumn::make('uuid')
                ->label('UUID'),

            ExportColumn::make('rfc_emisor')
                ->label('RFC Emisor'),

            ExportColumn::make('nombre_emisor')
                ->label('Emisor'),

            ExportColumn::make('rfc_receptor')
                ->label('RFC Receptor'),

            ExportColumn::make('nombre_receptor')
                ->label('Receptor'),

            ExportColumn::make('concepto_principal')
                ->label('Concepto'),

            ExportColumn::make('subtotal')
                ->label('Subtotal'),

            ExportColumn::make('descuento')
                ->label('Descuento'),

            ExportColumn::make('iva')
                ->label('IVA'),

            ExportColumn::make('isr_retenido')
                ->label('ISR Retenido'),

            ExportColumn::make('iva_retenido')
                ->label('IVA Retenido'),

            ExportColumn::make('total')
                ->label('Total'),

            ExportColumn::make('status')
                ->label('Estatus')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'procesado' => 'Procesado',
                    'pendiente' => 'Pendiente',
                    'error' => 'Error',
                    default => $state,
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;

        return "La exportación de facturas finalizó. Se exportaron {$count} ".($count === 1 ? 'registro' : 'registros').' correctamente.';
    }

    public function getFileName(Export $export): string
    {
        return 'facturas-'.$export->created_at->format('Y-m-d');
    }
}
