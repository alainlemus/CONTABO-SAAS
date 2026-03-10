<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Exports\InvoiceExporter;
use App\Filament\Resources\Invoices\Actions\ImportarXmlAction;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportarXmlAction::make()
                ->visible(fn (): bool => ! auth()->user()?->isViewer() && (bool) auth()->user()?->hasActiveAccess()),
            CreateAction::make()
                ->label('Nueva factura')
                ->visible(fn (): bool => ! auth()->user()?->isViewer() && (bool) auth()->user()?->hasActiveAccess()),
            ExportAction::make()
                ->label('Exportar')
                ->exporter(InvoiceExporter::class)
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ]),
        ];
    }
}
