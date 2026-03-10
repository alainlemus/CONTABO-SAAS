<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Exports\ComplianceReportExporter;
use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('compliance_report')
                ->label('Reporte de cumplimiento')
                ->icon('heroicon-o-document-chart-bar')
                ->color('gray')
                ->exporter(ComplianceReportExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ])
                ->modifyQueryUsing(function ($query, array $options) {
                    if (filled($options['period_year'] ?? null)) {
                        $query->where('period_year', $options['period_year']);
                    }

                    if (filled($options['period_month'] ?? null)) {
                        $query->where('period_month', $options['period_month']);
                    }

                    return $query;
                }),

            CreateAction::make()
                ->visible(fn (): bool => ! auth()->user()?->isViewer() && (bool) auth()->user()?->hasActiveAccess()),
        ];
    }
}
