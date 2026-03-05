<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $admin = User::where('email', 'admin@contabo.test')->firstOrFail();

        (new ClientSeeder)->run($admin);
        (new InvoiceSeeder)->run($admin);
        (new FiscalObligationSeeder)->run($admin);
    }
}
