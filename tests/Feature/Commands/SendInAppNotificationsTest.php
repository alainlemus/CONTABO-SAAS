<?php

namespace Tests\Feature\Commands;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendInAppNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Client::flushEventListeners();
    }

    public function test_admin_with_overdue_obligations_receives_danger_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        $this->artisan('app:send-inapp-notifications')->assertSuccessful();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);

        $notification = $admin->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('danger', $notification->data['status']);
        $this->assertStringContainsString('vencidas', $notification->data['title']);
    }

    public function test_admin_with_due_soon_obligations_receives_warning_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IvaMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(2),
        ]);

        $this->artisan('app:send-inapp-notifications')->assertSuccessful();

        $notification = $admin->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertEquals('warning', $notification->data['status']);
        $this->assertStringContainsString('próximas', $notification->data['title']);
    }

    public function test_admin_without_obligations_does_not_receive_notifications(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->artisan('app:send-inapp-notifications')->assertSuccessful();

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_inactive_admin_does_not_receive_notifications(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => false]);
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(3),
        ]);

        $this->artisan('app:send-inapp-notifications')->assertSuccessful();

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_command_outputs_number_of_notifications_sent(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Overdue,
            'due_date' => now()->subDays(5),
        ]);

        FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IvaMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(2),
        ]);

        $this->artisan('app:send-inapp-notifications')
            ->expectsOutputToContain('2')
            ->assertSuccessful();
    }
}
