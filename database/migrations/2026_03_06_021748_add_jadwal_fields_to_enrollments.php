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
            // Cek 'per_minggu' (opsional jika kamu masih butuh)
            if (!Schema::hasColumn('enrollments', 'per_minggu')) {
                $table->integer('per_minggu')->nullable()->after('program_id');
            }
            
            // INI PENTING: Menambahkan 'jumlah_pertemuan' agar dashboard tidak error
            if (!Schema::hasColumn('enrollments', 'jumlah_pertemuan')) {
                $table->integer('jumlah_pertemuan')->default(0)->after('per_minggu');
            }

            if (!Schema::hasColumn('enrollments', 'extra_hours')) {
                $table->integer('extra_hours')->default(0)->after('jumlah_pertemuan');
            }

            if (!Schema::hasColumn('enrollments', 'jadwal_detail')) {
                $table->text('jadwal_detail')->nullable()->after('extra_hours');
            }

            // Ubah 'ambil_mengaji' jadi string agar bisa dicek === 'ya' di Blade
            if (!Schema::hasColumn('enrollments', 'ambil_mengaji')) {
                $table->string('ambil_mengaji')->default('tidak')->after('jadwal_detail');
            }
        });
    }

    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['per_minggu', 'jumlah_pertemuan', 'extra_hours', 'jadwal_detail', 'ambil_mengaji']);
        });
    }
};