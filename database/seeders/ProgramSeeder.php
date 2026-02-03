<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('programs')->insert([
            [
                'name' => 'Program Reguler',
                'type' => 'reguler',
                'price' => 150000,
                'description' => 'Pendalaman materi harian untuk membantu tugas sekolah & ujian semester.',
                'created_at' => now(),
            ],
            [
                'name' => 'Program Intensif',
                'type' => 'intensif',
                'price' => 500000,
                'description' => 'Persiapan khusus UTBK, CPNS, dan Sekolah Kedinasan. Fokus latihan soal.',
                'created_at' => now(),
            ]
        ]);
    }
}