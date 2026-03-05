<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Firm;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Firm::all()->each(function (Firm $firm): void {
            Client::factory()
                ->count(5)
                ->for($firm)
                ->create();
        });
    }
}
