<?php

namespace App\Filament\Resources\FiscalObligations\Pages;

use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFiscalObligation extends EditRecord
{
    protected static string $resource = FiscalObligationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_acuse')
                ->label('Descargar acuse PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (): string => route('fiscal-obligations.download.acuse', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->acusePdfExists()),

            DeleteAction::make(),
        ];
    }
}
