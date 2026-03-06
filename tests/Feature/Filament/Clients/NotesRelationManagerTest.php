<?php

namespace Tests\Feature\Filament\Clients;

use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\RelationManagers\NotesRelationManager;
use App\Models\Client;
use App\Models\ClientNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $this->admin->id]);
    }

    public function test_notes_tab_visible_on_edit_client_page(): void
    {
        $this->actingAs($this->admin)
            ->get(EditClient::getUrl(['record' => $this->client]))
            ->assertSuccessful();
    }

    public function test_lists_existing_notes_for_client(): void
    {
        $note = ClientNote::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->admin->id,
            'type' => 'general',
            'body' => 'Esta es una nota de prueba.',
        ]);

        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->assertCanSeeTableRecords([$note]);
    }

    public function test_can_create_note(): void
    {
        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->callTableAction('create', data: [
                'type' => 'alert',
                'body' => 'Alerta importante para el cliente.',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('client_notes', [
            'client_id' => $this->client->id,
            'user_id' => $this->admin->id,
            'type' => 'alert',
            'body' => 'Alerta importante para el cliente.',
        ]);
    }

    public function test_can_edit_note(): void
    {
        $note = ClientNote::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->admin->id,
            'type' => 'general',
            'body' => 'Nota original.',
        ]);

        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->callTableAction('edit', $note, data: [
                'type' => 'reminder',
                'body' => 'Nota actualizada.',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('client_notes', [
            'id' => $note->id,
            'type' => 'reminder',
            'body' => 'Nota actualizada.',
        ]);
    }

    public function test_can_delete_note(): void
    {
        $note = ClientNote::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->callTableAction('delete', $note)
            ->assertHasNoTableActionErrors();

        $this->assertModelMissing($note);
    }

    public function test_create_note_requires_body(): void
    {
        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->callTableAction('create', data: [
                'type' => 'general',
                'body' => '',
            ])
            ->assertHasTableActionErrors(['body']);
    }

    public function test_does_not_show_notes_from_other_clients(): void
    {
        $otherClient = Client::factory()->create(['user_id' => $this->admin->id]);
        $otherNote = ClientNote::factory()->create([
            'client_id' => $otherClient->id,
            'user_id' => $this->admin->id,
        ]);

        Livewire::actingAs($this->admin)
            ->test(NotesRelationManager::class, [
                'ownerRecord' => $this->client,
                'pageClass' => EditClient::class,
            ])
            ->assertCanNotSeeTableRecords([$otherNote]);
    }
}
