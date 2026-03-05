<?php

namespace Database\Seeders;

use App\Models\Firm;
use Illuminate\Database\Seeder;

class FirmSeeder extends Seeder
{
    public function run(): void
    {
        Firm::factory()
            ->count(3)
            ->create();
    }
}
