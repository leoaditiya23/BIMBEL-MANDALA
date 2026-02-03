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
        // Panggil UserSeeder terlebih dahulu (Admin, Mentor, Siswa)
        // Kemudian panggil ProgramSeeder (Reguler, Intensif)
        $this->call([
            UserSeeder::class,
            ProgramSeeder::class,
        ]);
        
        // Baris bawaan factory di bawah ini bisa kamu hapus atau biarkan dikomentari
        // \App\Models\User::factory(10)->create();
    }
}