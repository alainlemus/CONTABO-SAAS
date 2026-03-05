<?php

namespace App\Filament\Resources\Team\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del miembro')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn (?string $state): bool => filled($state)),

                    Select::make('role')
                        ->label('Rol')
                        ->options([
                            UserRole::Capturista->value => UserRole::Capturista->label(),
                            UserRole::Viewer->value => UserRole::Viewer->label(),
                        ])
                        ->required()
                        ->default(UserRole::Capturista->value),
                ]),
        ]);
    }
}
