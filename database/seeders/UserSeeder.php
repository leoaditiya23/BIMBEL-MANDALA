<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Mandala',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Mentor
        User::create([
            'name' => 'Tutor Budi',
            'email' => 'mentor@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);

        // Siswa
        User::create([
            'name' => 'Siswa Archel',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);
    }
}