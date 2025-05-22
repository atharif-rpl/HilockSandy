<?php

// database/seeders/UserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang kuat
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang kuat
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Tambahkan lebih banyak user jika perlu
        // User::factory(5)->create(); // Jika Anda punya UserFactory
    }
}