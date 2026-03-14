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
    // Hanya tambah kolom jika belum ada
    if (!Schema::hasColumn('programs', 'jam_mulai')) {
        Schema::table('programs', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable();
        });
    }
}

public function down(): void
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn(['jam_mulai', 'hari']);
    });
}
};
