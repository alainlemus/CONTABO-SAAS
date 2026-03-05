<?php

namespace App\Filament\Resources\FiscalObligations\Pages;

use App\Filament\Resources\FiscalObligations\FiscalObligationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFiscalObligations extends ListRecords
{
    protected static string $resource = FiscalObligationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
