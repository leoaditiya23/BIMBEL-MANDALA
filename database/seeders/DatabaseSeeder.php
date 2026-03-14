<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('programs')->truncate();
        
        // Buat tabel FAQ jika belum ada (Darurat)
        Schema::dropIfExists('faqs');
        Schema::create('faqs', function ($table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->timestamps();
        });

        // 1. Isi Data User (Admin, Mentor, Siswa)
        DB::table('users')->insert([
            ['name' => 'Admin Pusat', 'email' => 'admin@gmail.com', 'password' => Hash::make('password'), 'role' => 'admin'],
            ['name' => 'Budi Mentor', 'email' => 'mentor@gmail.com', 'password' => Hash::make('password'), 'role' => 'mentor'],
            ['name' => 'Siswa Pintar', 'email' => 'siswa@gmail.com', 'password' => Hash::make('password'), 'role' => 'siswa'],
        ]);

        // 2. Isi Data Program (Agar Home Berisi)
        DB::table('programs')->insert([
            ['name' => 'Matematika Intensif', 'jenjang' => 'SMA', 'type' => 'Reguler', 'created_at' => now()],
            ['name' => 'English Conversation', 'jenjang' => 'SMP', 'type' => 'Privat', 'created_at' => now()],
            ['name' => 'Fisika Dasar', 'jenjang' => 'SMA', 'type' => 'Reguler', 'created_at' => now()],
        ]);

        // 3. Isi Data FAQ (Agar Home Tidak Error & Berisi)
        DB::table('faqs')->insert([
            ['question' => 'Bagaimana cara mendaftar?', 'answer' => 'Klik tombol Daftar di pojok kanan atas.', 'created_at' => now()],
            ['question' => 'Apakah bisa les online?', 'answer' => 'Ya, kami menyediakan layanan via Zoom.', 'created_at' => now()],
            ['question' => 'Kapan jadwal belajar ditentukan?', 'answer' => 'Setelah pembayaran diverifikasi oleh admin.', 'created_at' => now()],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}