<?php

namespace App\Filament\Resources\FiscalObligations;

use App\Filament\Resources\FiscalObligations\Pages\CreateFiscalObligation;
use App\Filament\Resources\FiscalObligations\Pages\EditFiscalObligation;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Filament\Resources\FiscalObligations\Schemas\FiscalObligationForm;
use App\Filament\Resources\FiscalObligations\Tables\FiscalObligationsTable;
use App\Models\FiscalObligation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FiscalObligationResource extends Resource
{
    protected static ?string $model = FiscalObligation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $modelLabel = 'Obligación Fiscal';

    protected static ?string $pluralModelLabel = 'Obligaciones Fiscales';

    protected static string|\UnitEnum|null $navigationGroup = 'Cartera';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return FiscalObligationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FiscalObligationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFiscalObligations::route('/'),
            'create' => CreateFiscalObligation::route('/create'),
            'edit' => EditFiscalObligation::route('/{record}/edit'),
        ];
    }
}
