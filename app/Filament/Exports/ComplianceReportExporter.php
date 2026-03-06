<?php

namespace App\Filament\Exports;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\FiscalObligation;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;

class ComplianceReportExporter extends Exporter
{
    protected static ?string $model = FiscalObligation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('client.name')
                ->label('Cliente'),

            ExportColumn::make('client.tax_id')
                ->label('RFC'),

            ExportColumn::make('client.person_type')
                ->label('Tipo de persona')
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'fisica' => 'Física',
                    'moral' => 'Moral',
                    default => $state ?? '',
                }),

            ExportColumn::make('client.tax_regime')
                ->label('Régimen fiscal'),

            ExportColumn::make('type')
                ->label('Obligación')
                ->formatStateUsing(fn (ObligationType $state): string => $state->label()),

            ExportColumn::make('period_label')
                ->label('Período')
                ->state(fn (FiscalObligation $record): string => $record->periodLabel()),

            ExportColumn::make('due_date')
                ->label('Fecha de vencimiento')
                ->formatStateUsing(fn ($state): string => $state ? Carbon::parse($state)->format('d/m/Y') : ''),

            ExportColumn::make('status')
                ->label('Estatus')
                ->formatStateUsing(fn (ObligationStatus $state): string => $state->label()),

            ExportColumn::make('presented_at')
                ->label('Presentada el')
                ->formatStateUsing(fn ($state): string => $state ? Carbon::parse($state)->format('d/m/Y') : ''),

            ExportColumn::make('reference')
                ->label('Número de acuse / referencia SAT'),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        $currentYear = now()->year;

        return [
            Select::make('period_year')
                ->label('Año')
                ->options(array_combine(
                    range($currentYear - 2, $currentYear + 1),
                    range($currentYear - 2, $currentYear + 1),
                ))
                ->default($currentYear)
                ->required(),

            Select::make('period_month')
                ->label('Mes')
                ->options([
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
                    4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
                    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
                    10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                ])
                ->default(now()->month)
                ->placeholder('Todos los meses'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $count = $export->successful_rows;

        return "El reporte de cumplimiento finalizó. Se exportaron {$count} ".($count === 1 ? 'registro' : 'registros').' correctamente.';
    }

    public function getFileName(Export $export): string
    {
        return 'reporte-cumplimiento-'.$export->created_at->format('Y-m-d');
    }
}
