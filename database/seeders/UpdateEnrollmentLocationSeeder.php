<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateEnrollmentLocationSeeder extends Seeder
{
    public function run()
    {
        $enrollments = DB::table('enrollments')->get();

        foreach ($enrollments as $enrollment) {
            $user = DB::table('users')->where('id', $enrollment->user_id)->first();
            
            DB::table('enrollments')->where('id', $enrollment->id)->update([
                'lokasi_cabang' => 'Cabang Pusat Semarang',
                'alamat_siswa'  => $user->alamat ?? 'Alamat belum diisi di profil',
            ]);
        }
        $this->command->info('Data lama berhasil diperbarui!');
    }
}