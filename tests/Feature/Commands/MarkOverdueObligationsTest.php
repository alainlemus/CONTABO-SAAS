<?php

namespace Tests\Feature\Commands;

use App\Enums\ObligationStatus;
use App\Enums\ObligationType;
use App\Models\Client;
use App\Models\FiscalObligation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkOverdueObligationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Client::flushEventListeners();
    }

    public function test_marks_pending_obligations_past_due_date_as_overdue(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $overdue = FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->subDay(),
        ]);

        $this->artisan('app:mark-overdue-obligations')->assertSuccessful();

        $this->assertEquals(ObligationStatus::Overdue, $overdue->fresh()->status);
    }

    public function test_does_not_mark_future_pending_obligations_as_overdue(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $future = FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IsrMensual,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->addDays(5),
        ]);

        $this->artisan('app:mark-overdue-obligations')->assertSuccessful();

        $this->assertEquals(ObligationStatus::Pending, $future->fresh()->status);
    }

    public function test_does_not_change_already_presented_obligations(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        $presented = FiscalObligation::factory()->create([
            'client_id' => $client->id,
            'type' => ObligationType::IvaMensual,
            'status' => ObligationStatus::Presented,
            'due_date' => now()->subDays(10),
        ]);

        $this->artisan('app:mark-overdue-obligations')->assertSuccessful();

        $this->assertEquals(ObligationStatus::Presented, $presented->fresh()->status);
    }

    public function test_outputs_count_of_marked_obligations(): void
    {
        $admin = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $admin->id]);

        FiscalObligation::factory()->count(3)->create([
            'client_id' => $client->id,
            'status' => ObligationStatus::Pending,
            'due_date' => now()->subDay(),
        ]);

        $this->artisan('app:mark-overdue-obligations')
            ->expectsOutputToContain('3')
            ->assertSuccessful();
    }

    public function test_returns_zero_when_no_obligations_to_mark(): void
    {
        $this->artisan('app:mark-overdue-obligations')
            ->expectsOutputToContain('0')
            ->assertSuccessful();
    }
}
