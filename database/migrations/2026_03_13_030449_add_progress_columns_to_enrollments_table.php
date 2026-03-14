<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        // Hanya buat jika kolom BELUM ada
        if (!Schema::hasColumn('enrollments', 'jumlah_pertemuan')) {
            $table->integer('jumlah_pertemuan')->default(8)->after('mapel');
        }
        
        if (!Schema::hasColumn('enrollments', 'pertemuan_selesai')) {
            $table->integer('pertemuan_selesai')->default(0)->after('jumlah_pertemuan');
        }

        if (!Schema::hasColumn('enrollments', 'mentor_id')) {
            $table->unsignedBigInteger('mentor_id')->nullable()->after('user_id');
        }
    });
}

public function down(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropColumn(['jumlah_pertemuan', 'pertemuan_selesai']);
    });
}
};
