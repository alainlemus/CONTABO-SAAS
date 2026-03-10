<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Datos válidos de base para los tests. */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'SecurePass1',
            'password_confirmation' => 'SecurePass1',
        ], $overrides);
    }

    private function postRegister(array $data): \Illuminate\Testing\TestResponse
    {
        return $this->post('/register', $data);
    }

    // ── Happy path ───────────────────────────────────────────────────────────

    public function test_registro_exitoso_crea_usuario_y_redirige(): void
    {
        $response = $this->postRegister($this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
        $this->assertAuthenticated();
    }

    // ── Nombre ───────────────────────────────────────────────────────────────

    public function test_nombre_es_obligatorio(): void
    {
        $this->postRegister($this->validPayload(['name' => '']))
            ->assertSessionHasErrors(['name' => 'El nombre es obligatorio.']);
    }

    public function test_nombre_minimo_2_caracteres(): void
    {
        $this->postRegister($this->validPayload(['name' => 'A']))
            ->assertSessionHasErrors(['name' => 'El nombre debe tener al menos 2 caracteres.']);
    }

    public function test_nombre_maximo_255_caracteres(): void
    {
        $this->postRegister($this->validPayload(['name' => str_repeat('A', 256)]))
            ->assertSessionHasErrors(['name' => 'El nombre no puede superar los 255 caracteres.']);
    }

    // ── Correo electrónico ───────────────────────────────────────────────────

    public function test_email_es_obligatorio(): void
    {
        $this->postRegister($this->validPayload(['email' => '']))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio.']);
    }

    public function test_email_formato_invalido(): void
    {
        $this->postRegister($this->validPayload(['email' => 'no-es-un-email']))
            ->assertSessionHasErrors(['email' => 'Ingresa un correo electrónico válido.']);
    }

    public function test_email_maximo_255_caracteres(): void
    {
        $local = str_repeat('a', 244);
        $this->postRegister($this->validPayload(['email' => "{$local}@example.com"]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico no puede superar los 255 caracteres.']);
    }

    public function test_email_duplicado_muestra_mensaje(): void
    {
        User::factory()->create(['email' => 'juan@example.com']);

        $this->postRegister($this->validPayload(['email' => 'juan@example.com']))
            ->assertSessionHasErrors(['email' => 'Este correo electrónico ya está registrado. ¿Olvidaste tu contraseña?']);
    }

    // ── Contraseña ───────────────────────────────────────────────────────────

    public function test_password_es_obligatorio(): void
    {
        $this->postRegister($this->validPayload(['password' => '', 'password_confirmation' => '']))
            ->assertSessionHasErrors(['password' => 'La contraseña es obligatoria.']);
    }

    public function test_password_minimo_8_caracteres(): void
    {
        $this->postRegister($this->validPayload(['password' => 'short', 'password_confirmation' => 'short']))
            ->assertSessionHasErrors(['password' => 'La contraseña debe tener al menos 8 caracteres.']);
    }

    public function test_password_maximo_100_caracteres(): void
    {
        $long = str_repeat('A', 101);
        $this->postRegister($this->validPayload(['password' => $long, 'password_confirmation' => $long]))
            ->assertSessionHasErrors(['password' => 'La contraseña no puede superar los 100 caracteres.']);
    }

    public function test_password_no_coincide_con_confirmacion(): void
    {
        $this->postRegister($this->validPayload(['password_confirmation' => 'OtraPassword1']))
            ->assertSessionHasErrors(['password' => 'Las contraseñas no coinciden.']);
    }

    // ── Confirmación de contraseña ───────────────────────────────────────────

    public function test_password_confirmation_es_obligatorio(): void
    {
        $this->postRegister($this->validPayload(['password_confirmation' => '']))
            ->assertSessionHasErrors(['password_confirmation' => 'Debes confirmar tu contraseña.']);
    }
}
