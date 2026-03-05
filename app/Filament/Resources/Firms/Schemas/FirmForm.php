<?php

namespace App\Filament\Resources\Firms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class FirmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nombre del despacho')
                        ->required()
                        ->columnSpan(2),
                    TextInput::make('tax_id')
                        ->label('RFC')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(13),
                    Select::make('billing_plan')
                        ->label('Plan de facturación')
                        ->options([
                            'starter' => 'Starter',
                            'growth'  => 'Growth',
                            'scale'   => 'Scale',
                        ])
                        ->required()
                        ->default('starter'),
                    TextInput::make('contact_email')
                        ->label('Correo de contacto')
                        ->email(),
                    TextInput::make('contact_phone')
                        ->label('Teléfono de contacto')
                        ->tel(),
                    DatePicker::make('billing_expires_at')
                        ->label('Vencimiento del plan'),
                ]),
            ]);
    }
}
