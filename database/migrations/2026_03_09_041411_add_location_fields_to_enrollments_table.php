<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('enrollments', function (Blueprint $table) {
        // Cek dulu sebelum buat lokasi_cabang
        if (!Schema::hasColumn('enrollments', 'lokasi_cabang')) {
            $table->string('lokasi_cabang')->nullable()->after('mapel');
        }
        
        // Cek dulu sebelum buat alamat_siswa
        if (!Schema::hasColumn('enrollments', 'alamat_siswa')) {
            $table->text('alamat_siswa')->nullable()->after('lokasi_cabang');
        }
    });
}

public function down()
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropColumn(['lokasi_cabang', 'alamat_siswa']);
    });
}
};
