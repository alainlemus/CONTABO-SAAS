<?php

namespace Tests\Feature\Mail;

use App\Jobs\SendWelcomeEmail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WelcomeMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_is_queued_after_registration(): void
    {
        Queue::fake();

        $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        Queue::assertPushed(SendWelcomeEmail::class, function (SendWelcomeEmail $job) {
            return $job->user->email === 'juan@example.com';
        });
    }

    public function test_welcome_mail_is_sent_to_user(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        (new SendWelcomeEmail($user))->handle();

        Mail::assertSent(WelcomeMail::class, function (WelcomeMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_welcome_mail_has_correct_subject(): void
    {
        $user = User::factory()->create();
        $mail = new WelcomeMail($user);

        $this->assertStringContainsString('Bienvenido', $mail->envelope()->subject);
    }

    public function test_welcome_mail_uses_correct_view(): void
    {
        $user = User::factory()->create();
        $mail = new WelcomeMail($user);

        $this->assertEquals('emails.welcome', $mail->content()->view);
    }
}
