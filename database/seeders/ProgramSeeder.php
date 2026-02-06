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
                'name' => 'Reguler SD - Matematika',
                'jenjang' => 'SD',
                'type' => 'reguler',
                'price' => 500000,
                'mentor_id' => 2, // ID si Budi Mentor
                'created_at' => now(),
            ],
            [
                'name' => 'Intensif UTBK - Soshum',
                'jenjang' => 'SMA',
                'type' => 'intensif',
                'price' => 1500000,
                'mentor_id' => 2,
                'created_at' => now(),
            ],
        ]);
    }
}