<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament.layouts.login';

    /**
     * Apunta el hint "¿Olvidaste tu contraseña?" del campo password
     * a nuestra página custom en lugar de la de Filament.
     */
    public function getPasswordResetUrl(): ?string
    {
        return route('password.request');
    }
}
