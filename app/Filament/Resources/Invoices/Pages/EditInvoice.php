<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
