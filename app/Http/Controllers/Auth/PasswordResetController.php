<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /** Muestra el formulario para solicitar el enlace de restablecimiento. */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el correo con el enlace de restablecimiento.
     * Siempre responde con el mismo mensaje para no revelar si el email existe.
     */
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink(
            $request->only('email'),
            function ($user, string $token): void {
                Mail::to($user->email)->send(new ResetPasswordMail($user, $token));
            },
        );

        return back()->with(
            'status',
            'Si ese correo está registrado, recibirás un enlace para restablecer tu contraseña en breve.',
        );
    }

    /** Muestra el formulario para ingresar la nueva contraseña. */
    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /** Aplica la nueva contraseña y redirige al login. */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('filament.admin.auth.login')
                ->with('status', '¡Contraseña restablecida! Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $this->statusMessage($status)]);
    }

    /** Traduce los códigos de error de Password broker al español. */
    private function statusMessage(string $status): string
    {
        return match ($status) {
            Password::INVALID_TOKEN => 'El enlace de restablecimiento es inválido o ha expirado. Solicita uno nuevo.',
            'passwords.throttled' => 'Demasiados intentos. Espera unos minutos antes de volver a intentarlo.',
            default => 'No encontramos un usuario con ese correo electrónico.',
        };
    }
}
