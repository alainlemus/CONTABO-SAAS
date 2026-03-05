<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_cer')
                ->label('Descargar CER')
                ->icon('heroicon-o-shield-check')
                ->color('success')
                ->url(fn (): string => route('clients.download.cer', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->record->efirma_cer_path && auth()->user()?->isAdmin()),

            Action::make('download_key')
                ->label('Descargar KEY')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->url(fn (): string => route('clients.download.key', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->record->efirma_key_path && auth()->user()?->isAdmin()),

            DeleteAction::make(),
        ];
    }
}
