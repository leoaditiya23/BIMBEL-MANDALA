<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Admin Mandala',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Akun Mentor
        User::create([
            'name' => 'Budi Mentor',
            'email' => 'mentor@gmail.com',
            'password' => Hash::make('mentor123'),
            'role' => 'mentor',
        ]);

        // Akun Siswa
        User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);
    }
}