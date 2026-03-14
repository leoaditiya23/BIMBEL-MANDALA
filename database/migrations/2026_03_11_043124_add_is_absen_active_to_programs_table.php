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
    // Cek dulu apakah kolom 'is_absen_active' sudah ada
    if (!Schema::hasColumn('programs', 'is_absen_active')) {
        Schema::table('programs', function (Blueprint $table) {
            $table->boolean('is_absen_active')->default(false)->after('hari');
        });
    }
}

public function down()
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn('is_absen_active');
    });
}
};
