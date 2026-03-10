<?php

namespace Tests\Feature\Auth;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // ── GET /forgot-password ─────────────────────────────────────────────────

    public function test_formulario_forgot_password_es_accesible(): void
    {
        $this->get('/forgot-password')
            ->assertStatus(200)
            ->assertViewIs('auth.forgot-password');
    }

    public function test_usuario_autenticado_no_puede_ver_forgot_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/forgot-password')
            ->assertRedirect();
    }

    // ── POST /forgot-password — validación ───────────────────────────────────

    public function test_email_es_obligatorio_en_forgot_password(): void
    {
        $this->post('/forgot-password', ['email' => ''])
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio.']);
    }

    public function test_email_invalido_en_forgot_password(): void
    {
        $this->post('/forgot-password', ['email' => 'no-es-un-email'])
            ->assertSessionHasErrors(['email' => 'Ingresa un correo electrónico válido.']);
    }

    // ── POST /forgot-password — comportamiento ────────────────────────────────

    public function test_email_no_registrado_muestra_mensaje_generico_sin_revelar_existencia(): void
    {
        Mail::fake();

        $response = $this->post('/forgot-password', ['email' => 'noexiste@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas(
            'status',
            'Si ese correo está registrado, recibirás un enlace para restablecer tu contraseña en breve.',
        );

        Mail::assertNothingSent();
    }

    public function test_email_registrado_envia_correo_de_reset(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'usuario@example.com']);

        $response = $this->post('/forgot-password', ['email' => 'usuario@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas(
            'status',
            'Si ese correo está registrado, recibirás un enlace para restablecer tu contraseña en breve.',
        );

        Mail::assertSent(ResetPasswordMail::class, fn ($mail) => $mail->hasTo('usuario@example.com'));
    }

    // ── GET /reset-password/{token} ──────────────────────────────────────────

    public function test_formulario_reset_password_es_accesible(): void
    {
        $this->get('/reset-password/fake-token-123')
            ->assertStatus(200)
            ->assertViewIs('auth.reset-password')
            ->assertViewHas('token', 'fake-token-123');
    }

    public function test_usuario_autenticado_no_puede_ver_reset_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/reset-password/fake-token-123')
            ->assertRedirect();
    }

    // ── POST /reset-password — validación ────────────────────────────────────

    public function test_token_es_obligatorio(): void
    {
        $this->post('/reset-password', [
            'token' => '',
            'email' => 'usuario@example.com',
            'password' => 'NuevaPass1',
            'password_confirmation' => 'NuevaPass1',
        ])->assertSessionHasErrors(['token' => 'El token de restablecimiento es inválido.']);
    }

    public function test_email_es_obligatorio_en_reset_password(): void
    {
        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => '',
            'password' => 'NuevaPass1',
            'password_confirmation' => 'NuevaPass1',
        ])->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio.']);
    }

    public function test_password_es_obligatorio_en_reset(): void
    {
        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'usuario@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHasErrors(['password' => 'La nueva contraseña es obligatoria.']);
    }

    public function test_password_minimo_8_caracteres_en_reset(): void
    {
        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'usuario@example.com',
            'password' => 'corta',
            'password_confirmation' => 'corta',
        ])->assertSessionHasErrors(['password' => 'La contraseña debe tener al menos 8 caracteres.']);
    }

    public function test_password_maximo_100_caracteres_en_reset(): void
    {
        $long = str_repeat('A', 101);

        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'usuario@example.com',
            'password' => $long,
            'password_confirmation' => $long,
        ])->assertSessionHasErrors(['password' => 'La contraseña no puede superar los 100 caracteres.']);
    }

    public function test_password_no_coincide_en_reset(): void
    {
        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'usuario@example.com',
            'password' => 'NuevaPass1',
            'password_confirmation' => 'Diferente1',
        ])->assertSessionHasErrors(['password' => 'Las contraseñas no coinciden.']);
    }

    public function test_password_confirmation_es_obligatoria(): void
    {
        $this->post('/reset-password', [
            'token' => 'valid-token',
            'email' => 'usuario@example.com',
            'password' => 'NuevaPass1',
            'password_confirmation' => '',
        ])->assertSessionHasErrors(['password_confirmation' => 'Debes confirmar tu nueva contraseña.']);
    }

    // ── POST /reset-password — comportamiento ─────────────────────────────────

    public function test_token_invalido_muestra_error(): void
    {
        $user = User::factory()->create(['email' => 'usuario@example.com']);

        $this->post('/reset-password', [
            'token' => 'token-invalido',
            'email' => 'usuario@example.com',
            'password' => 'NuevaPass1',
            'password_confirmation' => 'NuevaPass1',
        ])->assertSessionHasErrors([
            'email' => 'El enlace de restablecimiento es inválido o ha expirado. Solicita uno nuevo.',
        ]);
    }

    public function test_reset_exitoso_cambia_password_y_redirige_al_login(): void
    {
        $user = User::factory()->create([
            'email' => 'usuario@example.com',
            'password' => Hash::make('PasswordAntigua1'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'usuario@example.com',
            'password' => 'NuevaPassword1',
            'password_confirmation' => 'NuevaPassword1',
        ]);

        $response->assertRedirect(route('filament.admin.auth.login'));
        $response->assertSessionHas(
            'status',
            '¡Contraseña restablecida! Ya puedes iniciar sesión.',
        );

        $user->refresh();
        $this->assertTrue(Hash::check('NuevaPassword1', $user->password));
    }
}
