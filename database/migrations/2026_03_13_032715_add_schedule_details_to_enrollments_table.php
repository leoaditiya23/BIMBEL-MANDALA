<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('enrollments', function (Blueprint $table) {
        // Cek satu per satu sebelum tambah agar tidak FAIL lagi
        if (!Schema::hasColumn('enrollments', 'hari')) {
            $table->string('hari')->nullable()->after('mapel');
        }
        if (!Schema::hasColumn('enrollments', 'jam_mulai')) {
            $table->time('jam_mulai')->nullable()->after('hari');
        }
        if (!Schema::hasColumn('enrollments', 'jam_selesai')) {
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        }
        if (!Schema::hasColumn('enrollments', 'link_zoom')) {
            $table->string('link_zoom')->nullable()->after('lokasi_cabang');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
        });
    }
};
