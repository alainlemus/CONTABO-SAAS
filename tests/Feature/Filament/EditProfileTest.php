<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(7),
        ]);
    }

    public function test_profile_page_loads_within_panel_layout(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/profile')
            ->assertSuccessful();
    }

    public function test_profile_page_is_not_simple(): void
    {
        $this->assertFalse(EditProfile::isSimple());
    }

    public function test_profile_page_title_is_in_spanish(): void
    {
        $this->assertEquals('Mi perfil', EditProfile::getLabel());
    }

    public function test_profile_form_fills_with_current_user_data(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditProfile::class)
            ->assertFormSet([
                'name' => $this->admin->name,
                'email' => $this->admin->email,
            ]);
    }

    public function test_profile_name_can_be_updated(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'Nuevo Nombre',
                'email' => $this->admin->email,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'id' => $this->admin->id,
            'name' => 'Nuevo Nombre',
        ]);
    }

    public function test_profile_name_is_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditProfile::class)
            ->fillForm(['name' => ''])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_profile_email_must_be_valid(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditProfile::class)
            ->fillForm(['email' => 'no-es-un-email'])
            ->call('save')
            ->assertHasFormErrors(['email' => 'email']);
    }

    public function test_profile_password_confirmation_required_when_password_filled(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'password' => 'NuevaPassword123!',
                'passwordConfirmation' => '',
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasFormErrors(['passwordConfirmation' => 'required']);
    }

    public function test_profile_avatar_is_uploaded_to_public_disk(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->image('avatar.jpg');

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => $this->admin->name,
                'email' => $this->admin->email,
                'avatar_url' => $file,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->admin->refresh();
        $this->assertNotNull($this->admin->avatar_url);
    }

    public function test_get_filament_avatar_url_returns_null_when_no_avatar(): void
    {
        $this->admin->avatar_url = null;

        $this->assertNull($this->admin->getFilamentAvatarUrl());
    }

    public function test_get_filament_avatar_url_returns_url_when_avatar_set(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/test.jpg', 'fake-content');

        $this->admin->avatar_url = 'avatars/test.jpg';

        $url = $this->admin->getFilamentAvatarUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('avatars/test.jpg', $url);
    }
}
