<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends \Filament\Auth\Pages\EditProfile
{
    public static function isSimple(): bool
    {
        return false;
    }

    public static function getLabel(): string
    {
        return 'Mi perfil';
    }

    protected function getAvatarFormComponent(): FileUpload
    {
        return FileUpload::make('avatar_url')
            ->label('Foto de perfil')
            ->image()
            ->imageEditor()
            ->circleCropper()
            ->disk('public')
            ->directory('avatars')
            ->visibility('public')
            ->maxSize(2048)
            ->columnSpanFull();
    }

    protected function getNameFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('name')
            ->label('Nombre')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('email')
            ->label('Correo electrónico')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->live(debounce: 500);
    }

    protected function getPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('password')
            ->label('Nueva contraseña')
            ->validationAttribute('nueva contraseña')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rule(Password::default())
            ->showAllValidationMessages()
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirmar contraseña')
            ->validationAttribute('confirmación de contraseña')
            ->password()
            ->autocomplete('new-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    protected function getCurrentPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('currentPassword')
            ->label('Contraseña actual')
            ->validationAttribute('contraseña actual')
            ->password()
            ->autocomplete('current-password')
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')) || ($get('email') !== $this->getUser()->getAttributeValue('email')))
            ->dehydrated(false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getAvatarFormComponent(),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }
}
