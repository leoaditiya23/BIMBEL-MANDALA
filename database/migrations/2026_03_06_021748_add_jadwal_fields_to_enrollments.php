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
        // Cek satu per satu, kalau belum ada baru buat
        if (!Schema::hasColumn('enrollments', 'per_minggu')) {
            $table->integer('per_minggu')->nullable()->after('program_id');
        }
        
        if (!Schema::hasColumn('enrollments', 'extra_hours')) {
            $table->integer('extra_hours')->default(0)->after('per_minggu');
        }

        if (!Schema::hasColumn('enrollments', 'jadwal_detail')) {
            $table->text('jadwal_detail')->nullable()->after('extra_hours');
        }

        if (!Schema::hasColumn('enrollments', 'ambil_mengaji')) {
            $table->boolean('ambil_mengaji')->default(false)->after('jadwal_detail');
        }
    });
}

public function down()
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropColumn(['per_minggu', 'jadwal_detail']);
    });
}
};
