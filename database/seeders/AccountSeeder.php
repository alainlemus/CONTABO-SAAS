<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Firm;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Firm::all()->each(function (Firm $firm): void {
            Account::factory()
                ->count(15)
                ->for($firm)
                ->create();
        });
    }
}
