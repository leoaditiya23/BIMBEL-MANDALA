<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder secara berurutan
        $this->call([
            UserSeeder::class,
            ProgramSeeder::class,
        ]);
    }
}