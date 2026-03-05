<?php

namespace Tests\Feature\Mail;

use App\Jobs\SendTrialEndingEmail;
use App\Mail\TrialEndingMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TrialEndingMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_job_for_users_whose_trial_ends_in_two_days(): void
    {
        Queue::fake();

        // Vence exactamente en 2 días
        User::factory()->create(['trial_ends_at' => now()->addDays(2)]);

        $this->artisan('app:notify-trial-ending-users')->assertSuccessful();

        Queue::assertPushed(SendTrialEndingEmail::class);
    }

    public function test_command_does_not_dispatch_job_for_users_not_expiring_in_two_days(): void
    {
        Queue::fake();

        // Vence en 10 días
        User::factory()->create(['trial_ends_at' => now()->addDays(10)]);
        // Ya venció
        User::factory()->create(['trial_ends_at' => now()->subDay()]);

        $this->artisan('app:notify-trial-ending-users')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_command_dispatches_job_only_for_matching_users(): void
    {
        Queue::fake();

        $expiring = User::factory()->create(['trial_ends_at' => now()->addDays(2)]);
        User::factory()->create(['trial_ends_at' => now()->addDays(10)]);

        $this->artisan('app:notify-trial-ending-users')->assertSuccessful();

        Queue::assertPushed(SendTrialEndingEmail::class, 1);
        Queue::assertPushed(SendTrialEndingEmail::class, function (SendTrialEndingEmail $job) use ($expiring) {
            return $job->user->is($expiring);
        });
    }

    public function test_trial_ending_mail_is_sent_to_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        (new SendTrialEndingEmail($user))->handle();

        Mail::assertSent(TrialEndingMail::class, function (TrialEndingMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_trial_ending_mail_has_correct_subject(): void
    {
        $user = User::factory()->create();
        $mail = new TrialEndingMail($user);

        $this->assertStringContainsString('2 días', $mail->envelope()->subject);
    }

    public function test_trial_ending_mail_uses_correct_view(): void
    {
        $user = User::factory()->create();
        $mail = new TrialEndingMail($user);

        $this->assertEquals('emails.trial-ending', $mail->content()->view);
    }
}
