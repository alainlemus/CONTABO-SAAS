<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Resources\Team\Pages\CreateTeamMember;
use App\Filament\Resources\Team\Pages\EditTeamMember;
use App\Filament\Resources\Team\Pages\ListTeamMembers;
use App\Filament\Resources\Team\TeamMemberResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamMemberResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_list_team_members_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(TeamMemberResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_list_shows_only_own_team_members(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);
        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $otherAdmin = User::factory()->create();
        $otherMember = User::factory()->capturista()->create(['owner_id' => $otherAdmin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListTeamMembers::class)
            ->assertCanSeeTableRecords([$capturista, $viewer])
            ->assertCanNotSeeTableRecords([$otherMember]);
    }

    public function test_create_team_member_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(TeamMemberResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_can_create_team_member(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Capturista Uno',
                'email' => 'capturista@test.com',
                'password' => 'secreto123',
                'role' => UserRole::Capturista->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Capturista Uno',
            'email' => 'capturista@test.com',
            'owner_id' => $this->admin->id,
            'role' => UserRole::Capturista->value,
        ]);
    }

    public function test_create_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
            ->test(CreateTeamMember::class)
            ->fillForm([
                'name' => '',
                'email' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'email', 'password']);
    }

    public function test_can_edit_team_member(): void
    {
        $member = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(EditTeamMember::class, ['record' => $member->getRouteKey()])
            ->fillForm(['role' => UserRole::Viewer->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'role' => UserRole::Viewer->value,
        ]);
    }

    public function test_can_delete_team_member_via_bulk_action(): void
    {
        $member = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListTeamMembers::class)
            ->callTableBulkAction('delete', [$member]);

        $this->assertModelMissing($member);
    }

    public function test_can_delete_team_member_via_row_action(): void
    {
        $member = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        Livewire::actingAs($this->admin)
            ->test(ListTeamMembers::class)
            ->callTableAction('delete', $member);

        $this->assertModelMissing($member);
    }

    public function test_can_toggle_member_active_to_inactive(): void
    {
        $member = User::factory()->capturista()->create([
            'owner_id' => $this->admin->id,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListTeamMembers::class)
            ->callTableAction('toggle_active', $member);

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'is_active' => false,
        ]);
    }

    public function test_can_toggle_member_inactive_to_active(): void
    {
        $member = User::factory()->capturista()->create([
            'owner_id' => $this->admin->id,
            'is_active' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ListTeamMembers::class)
            ->callTableAction('toggle_active', $member);

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'is_active' => true,
        ]);
    }

    public function test_inactive_user_cannot_access_panel(): void
    {
        $capturista = User::factory()->capturista()->create([
            'owner_id' => $this->admin->id,
            'is_active' => false,
        ]);

        $this->actingAs($capturista)
            ->get(TeamMemberResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_capturista_cannot_access_team_resource(): void
    {
        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(TeamMemberResource::getUrl('index'))
            ->assertForbidden();
    }
}
