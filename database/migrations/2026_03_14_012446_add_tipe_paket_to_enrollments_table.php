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
    // Hanya tambah kolom JIKA kolom tersebut belum ada
    if (!Schema::hasColumn('enrollments', 'tipe_paket')) {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('tipe_paket')->nullable()->after('jenjang');
        });
    }
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
