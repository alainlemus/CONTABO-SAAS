<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@contabo.test'],
            [
                'name' => 'Admin Demo',
                'email' => 'admin@contabo.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
                'trial_ends_at' => now()->addDays(14),
            ]
        );
    }
}
