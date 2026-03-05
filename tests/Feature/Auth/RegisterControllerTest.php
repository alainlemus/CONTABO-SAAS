<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─── GET /register ────────────────────────────────────────────────────────

    public function test_guest_can_view_register_form(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_authenticated_user_is_redirected_from_register_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect();
    }

    // ─── POST /register ───────────────────────────────────────────────────────

    public function test_successful_registration_creates_admin_user_with_trial(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/admin');

        $user = User::where('email', 'juan@example.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Admin, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->trial_ends_at);
        $this->assertTrue($user->trial_ends_at->isFuture());
    }

    public function test_successful_registration_authenticates_user(): void
    {
        $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Otro Usuario',
            'email' => 'existing@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_fails_with_mismatched_passwords(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different456',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_fails_with_missing_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_trial_ends_at_is_approximately_14_days_from_now(): void
    {
        $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $user = User::where('email', 'juan@example.com')->first();

        $this->assertEqualsWithDelta(
            now()->addDays(14)->timestamp,
            $user->trial_ends_at->timestamp,
            10 // segundos de tolerancia
        );
    }
}
