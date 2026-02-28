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
        // Tambahkan jam_selesai jika belum ada
        if (!Schema::hasColumn('programs', 'jam_selesai')) {
            $table->time('jam_selesai')->nullable();
        }
        
        // Tambahkan jenjang jika belum ada (karena diminta di query error kamu)
        if (!Schema::hasColumn('programs', 'jenjang')) {
            $table->string('jenjang')->nullable();
        }

        // Tambahkan hari jika tadi belum sukses masuk
        if (!Schema::hasColumn('programs', 'hari')) {
            $table->string('hari')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn(['jam_selesai', 'jenjang', 'hari']);
    });
}
};
