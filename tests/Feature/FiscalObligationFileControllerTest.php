<?php

namespace Tests\Feature;

use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FiscalObligationFileControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    private FiscalObligation $obligation;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create(['user_id' => $this->admin->id]);
        $this->obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_year' => 2025,
            'period_month' => 1,
        ]);
    }

    // ─── Guest ────────────────────────────────────────────────────────────────

    public function test_guest_cannot_download_acuse(): void
    {
        $this->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertRedirect('/admin/login');
    }

    // ─── Admin — descarga exitosa ──────────────────────────────────────────────

    public function test_admin_can_download_acuse_when_file_exists(): void
    {
        $path = 'fiscal-obligations/acuses/'.$this->obligation->id.'/acuse_sat.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 fake pdf content');

        $this->obligation->update(['acuse_pdf_path' => $path]);

        $this->actingAs($this->admin)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    // ─── Admin — 404 ──────────────────────────────────────────────────────────

    public function test_admin_gets_404_when_acuse_pdf_path_is_null(): void
    {
        $this->obligation->update(['acuse_pdf_path' => null]);

        $this->actingAs($this->admin)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertNotFound();
    }

    public function test_admin_gets_404_when_file_missing_from_disk(): void
    {
        $this->obligation->update(['acuse_pdf_path' => 'fiscal-obligations/acuses/999/nonexistent.pdf']);

        $this->actingAs($this->admin)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertNotFound();
    }

    // ─── Otros admin — 404 (el Global Scope oculta la obligación ajena) ───────

    public function test_other_admin_cannot_download_acuse(): void
    {
        $path = 'fiscal-obligations/acuses/'.$this->obligation->id.'/acuse_sat.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 fake pdf content');
        $this->obligation->update(['acuse_pdf_path' => $path]);

        $otherAdmin = User::factory()->create();

        // El Global Scope de FiscalObligation filtra por ownerId, por lo que
        // el route model binding devuelve 404 antes de llegar a la lógica de acceso.
        $this->actingAs($otherAdmin)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertNotFound();
    }

    // ─── Capturista ───────────────────────────────────────────────────────────

    public function test_capturista_can_download_acuse_of_own_admin(): void
    {
        $path = 'fiscal-obligations/acuses/'.$this->obligation->id.'/acuse_sat.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 fake pdf content');
        $this->obligation->update(['acuse_pdf_path' => $path]);

        $capturista = User::factory()->capturista()->create(['owner_id' => $this->admin->id]);

        // El capturista comparte ownerId con el admin, por lo que puede descargar.
        $this->actingAs($capturista)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertSuccessful()
            ->assertHeader('Content-Disposition');
    }

    public function test_capturista_of_other_admin_cannot_download_acuse(): void
    {
        $path = 'fiscal-obligations/acuses/'.$this->obligation->id.'/acuse_sat.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 fake pdf content');
        $this->obligation->update(['acuse_pdf_path' => $path]);

        $otherAdmin = User::factory()->create();
        $capturista = User::factory()->capturista()->create(['owner_id' => $otherAdmin->id]);

        $this->actingAs($capturista)
            ->get(route('fiscal-obligations.download.acuse', $this->obligation))
            ->assertNotFound();
    }
}
