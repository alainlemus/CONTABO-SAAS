<?php

namespace App\Filament\Exports;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\FiscalObligation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class FiscalObligationExporter extends Exporter
{
    protected static ?string $model = FiscalObligation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('client.name')
                ->label('Cliente'),

            ExportColumn::make('type')
                ->label('Tipo de obligación')
                ->formatStateUsing(fn (ObligationType $state): string => $state->label()),

            ExportColumn::make('period_label')
                ->label('Período')
                ->state(fn (FiscalObligation $record): string => $record->periodLabel()),

            ExportColumn::make('due_date')
                ->label('Fecha de vencimiento')
                ->formatStateUsing(fn ($state): string => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : ''),

            ExportColumn::make('status')
                ->label('Estatus')
                ->formatStateUsing(fn (ObligationStatus $state): string => $state->label()),

            ExportColumn::make('presented_at')
                ->label('Fecha de presentación')
                ->formatStateUsing(fn ($state): string => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : ''),

            ExportColumn::make('reference')
                ->label('Número de acuse / referencia SAT'),

            ExportColumn::make('notes')
                ->label('Notas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;

        return "La exportación de obligaciones fiscales finalizó. Se exportaron {$count} ".($count === 1 ? 'registro' : 'registros').' correctamente.';
    }

    public function getFileName(Export $export): string
    {
        return 'obligaciones-fiscales-'.$export->created_at->format('Y-m-d');
    }
}
