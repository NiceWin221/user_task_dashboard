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

        User::factory()->create([
            'name' => 'Atmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('testing12345'), // Pastikan password dienkripsi
            'role' => 'admin',
        ]);
    }
}
