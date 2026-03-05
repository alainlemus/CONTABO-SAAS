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
        if (! User::where('email', 'alainttlm@gmail.com')->exists()) {
            User::factory()->create([
                'name' => 'Alain Lemus',
                'email' => 'alainttlm@gmail.com',
                'password' => bcrypt('admin123'),
            ]);
        }

        $this->call([
            ClientSeeder::class,
        ]);
    }
}
