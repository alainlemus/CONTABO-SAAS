<?php

namespace Tests\Feature\Filament;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Filament\Resources\FiscalObligations\Pages\ListFiscalObligations;
use App\Mail\ObligationPresentedMail;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ObligationPresentedNotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->client = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => '601',
            'status' => 'active',
            'email' => 'cliente@example.com',
        ]);
    }

    public function test_mail_is_sent_to_client_when_obligation_is_marked_as_presented(): void
    {
        Mail::fake();

        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => now()->toDateString(),
                'reference' => 'SAT-12345',
            ]);

        Mail::assertQueued(ObligationPresentedMail::class, function (ObligationPresentedMail $mail) use ($obligation): bool {
            return $mail->obligation->id === $obligation->id
                && $mail->hasTo('cliente@example.com');
        });
    }

    public function test_obligation_status_is_updated_to_presented(): void
    {
        Mail::fake();

        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 2,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => now()->toDateString(),
                'reference' => 'SAT-67890',
            ]);

        $this->assertDatabaseHas('fiscal_obligations', [
            'id' => $obligation->id,
            'status' => ObligationStatus::Presented->value,
            'reference' => 'SAT-67890',
        ]);
    }

    public function test_mail_is_not_sent_when_client_has_no_email(): void
    {
        Mail::fake();

        $clientSinEmail = Client::factory()->create([
            'user_id' => $this->admin->id,
            'tax_regime' => '601',
            'status' => 'active',
            'email' => null,
        ]);

        $obligation = FiscalObligation::factory()->create([
            'client_id' => $clientSinEmail->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 3,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => now()->toDateString(),
            ]);

        Mail::assertNothingQueued();
    }

    public function test_mail_is_sent_for_overdue_obligation_when_marked_presented(): void
    {
        Mail::fake();

        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IvaMensual,
            'period_month' => 1,
            'period_year' => 2025,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(10),
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ListFiscalObligations::class)
            ->callTableAction('mark_presented', $obligation, data: [
                'presented_at' => now()->toDateString(),
                'reference' => 'SAT-TARDIO',
            ]);

        Mail::assertQueued(ObligationPresentedMail::class);
    }

    public function test_mailable_has_correct_subject(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 4,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
            'presented_at' => now(),
        ]);

        $mail = new ObligationPresentedMail($obligation);

        $this->assertStringContainsString('presentada', $mail->envelope()->subject);
    }

    public function test_mailable_uses_correct_view(): void
    {
        $obligation = FiscalObligation::factory()->create([
            'client_id' => $this->client->id,
            'type' => ObligationType::IsrMensual,
            'period_month' => 5,
            'period_year' => 2025,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10),
            'presented_at' => now(),
        ]);

        $mail = new ObligationPresentedMail($obligation);

        $this->assertEquals('emails.obligation-presented', $mail->content()->view);
    }
}
