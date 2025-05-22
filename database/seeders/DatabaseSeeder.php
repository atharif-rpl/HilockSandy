<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            InvoiceSeeder::class, // Panggil InvoiceSeeder setelah UserSeeder
        ]);
    }
}