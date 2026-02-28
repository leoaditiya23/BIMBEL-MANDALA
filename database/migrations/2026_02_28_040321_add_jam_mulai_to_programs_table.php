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
    Schema::table('programs', function (Blueprint $table) {
        // Kita hapus ->after('hari') agar tidak error jika kolom hari tidak ada
        $table->time('jam_mulai')->nullable();
        
        // Sekalian saja tambahkan kolom hari jika memang hilang dari database
        if (!Schema::hasColumn('programs', 'hari')) {
            $table->string('hari')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn(['jam_mulai', 'hari']);
    });
}
};
