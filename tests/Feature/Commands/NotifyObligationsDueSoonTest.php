<?php

namespace Tests\Feature\Commands;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Jobs\SendObligationDueSoonEmail;
use App\Mail\ObligationDueSoonMail;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotifyObligationsDueSoonTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_job_for_admin_with_obligations_due_in_given_days(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id, 'status' => 'active']);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('app:notify-obligations-due-soon', ['--days' => 3])->assertSuccessful();

        Queue::assertPushed(SendObligationDueSoonEmail::class, function (SendObligationDueSoonEmail $job) use ($admin) {
            return $job->user->is($admin) && $job->daysUntilDue === 3;
        });
    }

    public function test_does_not_dispatch_job_when_no_obligations_due_on_target_date(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(10)->toDateString(),
        ]);

        $this->artisan('app:notify-obligations-due-soon', ['--days' => 3])->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_does_not_notify_for_already_presented_obligations(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Presented,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('app:notify-obligations-due-soon', ['--days' => 3])->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_groups_multiple_obligations_into_single_job_per_admin(): void
    {
        Queue::fake();

        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->count(3)->create([
            'client_id' => $client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('app:notify-obligations-due-soon', ['--days' => 3])->assertSuccessful();

        Queue::assertPushed(SendObligationDueSoonEmail::class, 1);
        Queue::assertPushed(SendObligationDueSoonEmail::class, function (SendObligationDueSoonEmail $job) {
            return $job->obligations->count() === 3;
        });
    }

    public function test_sends_one_job_per_admin_when_multiple_admins_have_due_obligations(): void
    {
        Queue::fake();

        $admin1 = User::factory()->create();
        $client1 = Client::factory()->create(['user_id' => $admin1->id]);

        $admin2 = User::factory()->create();
        $client2 = Client::factory()->create(['user_id' => $admin2->id]);

        foreach ([$client1, $client2] as $client) {
            FiscalObligation::factory()->create([
                'client_id' => $client->id,
                'type' => ObligationType::IvaMensual,
                'status' => ObligationStatus::Pending,
                'due_date' => now()->addDays(3)->toDateString(),
            ]);
        }

        $this->artisan('app:notify-obligations-due-soon', ['--days' => 3])->assertSuccessful();

        Queue::assertPushed(SendObligationDueSoonEmail::class, 2);
    }

    public function test_job_sends_obligation_due_soon_mail(): void
    {
        Mail::fake();

        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $obligations = FiscalObligation::factory()->count(2)->create([
            'client_id' => $client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(3)->toDateString(),
        ])->collect();

        (new SendObligationDueSoonEmail($admin, $obligations, 3))->handle();

        Mail::assertSent(ObligationDueSoonMail::class, function (ObligationDueSoonMail $mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_obligation_due_soon_mail_has_correct_subject(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $obligations = FiscalObligation::factory()->count(2)->create([
            'client_id' => $client->id,
        ])->collect();

        $mail = new ObligationDueSoonMail($admin, $obligations, 3);

        $this->assertStringContainsString('3 días', $mail->envelope()->subject);
    }

    public function test_obligation_due_soon_mail_uses_correct_view(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $obligations = FiscalObligation::factory()->count(1)->create([
            'client_id' => $client->id,
        ])->collect();

        $mail = new ObligationDueSoonMail($admin, $obligations, 3);

        $this->assertEquals('emails.obligation-due-soon', $mail->content()->view);
    }
}
