<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Admin User
        User::forceCreate([
            'name' => 'Admin Dicatatin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Regular User
        User::forceCreate([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
