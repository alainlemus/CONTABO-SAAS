<?php

namespace App\Filament\Resources\FiscalObligations\Schemas;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class FiscalObligationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Obligación')
                    ->icon(Heroicon::OutlinedCalendarDays)
                    ->description('Qué obligación, para qué cliente y en qué período')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('client_id')
                            ->label('Cliente')
                            ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipo de obligación')
                            ->options(ObligationType::options())
                            ->required(),

                        Select::make('status')
                            ->label('Estatus')
                            ->options(ObligationStatus::options())
                            ->required()
                            ->default(ObligationStatus::Pending->value),

                        TextInput::make('period_year')
                            ->label('Año del período')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2099)
                            ->required()
                            ->default(now()->year),

                        TextInput::make('period_month')
                            ->label('Mes del período (1–12, vacío si es anual)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->nullable(),

                        DatePicker::make('due_date')
                            ->label('Fecha límite de presentación')
                            ->required()
                            ->displayFormat('d/m/Y'),

                        DatePicker::make('presented_at')
                            ->label('Fecha de presentación')
                            ->nullable()
                            ->displayFormat('d/m/Y'),
                    ]),

                Section::make('Referencia y notas')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(1)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('reference')
                            ->label('Número de acuse / referencia SAT')
                            ->maxLength(100)
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->nullable(),

                        FileUpload::make('acuse_pdf_path')
                            ->label('PDF del acuse SAT')
                            ->disk('local')
                            ->directory(fn ($record) => 'fiscal-obligations/acuses/'.($record?->id ?? 'new'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->nullable()
                            ->downloadable()
                            ->helperText('Sube el PDF del acuse de recibo emitido por el SAT (opcional).'),
                    ]),
            ]);
    }
}
