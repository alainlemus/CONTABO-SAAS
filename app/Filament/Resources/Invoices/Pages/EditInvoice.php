<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_xml')
                ->label('Descargar XML')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(fn (): string => route('invoices.download.xml', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->record->xml_path),

            Action::make('download_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-o-document')
                ->color('danger')
                ->url(fn (): string => route('invoices.download.pdf', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->record->pdf_path),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['xml_path'])) {
            $data['xml_path'] = $this->record->xml_path;
        }

        if (empty($data['pdf_path'])) {
            $data['pdf_path'] = $this->record->pdf_path;
        }

        return $data;
    }
}
