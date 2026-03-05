<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientFileControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $this->admin->id]);
    }

    // ─── Guest ────────────────────────────────────────────────────────────────

    public function test_guest_cannot_download_cer(): void
    {
        $this->get(route('clients.download.cer', $this->client))
            ->assertRedirect('/admin/login');
    }

    public function test_guest_cannot_download_key(): void
    {
        $this->get(route('clients.download.key', $this->client))
            ->assertRedirect('/admin/login');
    }

    // ─── Admin — descarga exitosa ──────────────────────────────────────────────

    public function test_admin_can_download_cer_when_file_exists(): void
    {
        $path = 'clients/efirma/XAXX010101000.cer';
        Storage::disk('local')->put($path, 'binary-cer-content');

        $this->client->update(['efirma_cer_path' => $path, 'tax_id' => 'XAXX010101000']);

        $this->actingAs($this->admin)
            ->get(route('clients.download.cer', $this->client))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    public function test_admin_can_download_key_when_file_exists(): void
    {
        $path = 'clients/efirma/XAXX010101000.key';
        Storage::disk('local')->put($path, 'binary-key-content');

        $this->client->update(['efirma_key_path' => $path, 'tax_id' => 'XAXX010101000']);

        $this->actingAs($this->admin)
            ->get(route('clients.download.key', $this->client))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    // ─── Admin — 404 ──────────────────────────────────────────────────────────

    public function test_admin_gets_404_when_cer_file_missing_from_disk(): void
    {
        $this->client->update(['efirma_cer_path' => 'clients/efirma/nonexistent.cer']);

        $this->actingAs($this->admin)
            ->get(route('clients.download.cer', $this->client))
            ->assertNotFound();
    }

    public function test_admin_gets_404_when_cer_path_is_null(): void
    {
        $this->client->update(['efirma_cer_path' => null]);

        $this->actingAs($this->admin)
            ->get(route('clients.download.cer', $this->client))
            ->assertNotFound();
    }

    public function test_admin_gets_404_when_key_file_missing_from_disk(): void
    {
        $this->client->update(['efirma_key_path' => 'clients/efirma/nonexistent.key']);

        $this->actingAs($this->admin)
            ->get(route('clients.download.key', $this->client))
            ->assertNotFound();
    }

    public function test_admin_gets_404_when_key_path_is_null(): void
    {
        $this->client->update(['efirma_key_path' => null]);

        $this->actingAs($this->admin)
            ->get(route('clients.download.key', $this->client))
            ->assertNotFound();
    }

    // ─── Otros admin — 404 (el Global Scope oculta el cliente ajeno) ────────────

    public function test_other_admin_cannot_download_cer(): void
    {
        $path = 'clients/efirma/other.cer';
        Storage::disk('local')->put($path, 'binary-cer-content');
        $this->client->update(['efirma_cer_path' => $path]);

        $otherAdmin = User::factory()->create();

        // El Global Scope filtra por ownerId, por lo que el route model binding
        // devuelve 404 antes de llegar a la policy (el registro es invisible).
        $this->actingAs($otherAdmin)
            ->get(route('clients.download.cer', $this->client))
            ->assertNotFound();
    }

    public function test_other_admin_cannot_download_key(): void
    {
        $path = 'clients/efirma/other.key';
        Storage::disk('local')->put($path, 'binary-key-content');
        $this->client->update(['efirma_key_path' => $path]);

        $otherAdmin = User::factory()->create();

        // El Global Scope filtra por ownerId, por lo que el route model binding
        // devuelve 404 antes de llegar a la policy (el registro es invisible).
        $this->actingAs($otherAdmin)
            ->get(route('clients.download.key', $this->client))
            ->assertNotFound();
    }

    // ─── Capturista y viewer — 403 ────────────────────────────────────────────

    public function test_capturista_cannot_download_cer(): void
    {
        $path = 'clients/efirma/capturista.cer';
        Storage::disk('local')->put($path, 'binary-cer-content');
        $this->client->update(['efirma_cer_path' => $path]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(route('clients.download.cer', $this->client))
            ->assertForbidden();
    }

    public function test_capturista_cannot_download_key(): void
    {
        $path = 'clients/efirma/capturista.key';
        Storage::disk('local')->put($path, 'binary-key-content');
        $this->client->update(['efirma_key_path' => $path]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($capturista)
            ->get(route('clients.download.key', $this->client))
            ->assertForbidden();
    }

    public function test_viewer_cannot_download_cer(): void
    {
        $path = 'clients/efirma/viewer.cer';
        Storage::disk('local')->put($path, 'binary-cer-content');
        $this->client->update(['efirma_cer_path' => $path]);

        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(route('clients.download.cer', $this->client))
            ->assertForbidden();
    }

    public function test_viewer_cannot_download_key(): void
    {
        $path = 'clients/efirma/viewer.key';
        Storage::disk('local')->put($path, 'binary-key-content');
        $this->client->update(['efirma_key_path' => $path]);

        $viewer = User::factory()->viewer()->create(['owner_id' => $this->admin->id]);

        $this->actingAs($viewer)
            ->get(route('clients.download.key', $this->client))
            ->assertForbidden();
    }
}
