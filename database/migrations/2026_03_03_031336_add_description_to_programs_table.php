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
    // Cek dulu apakah kolom 'description' sudah ada
    if (!Schema::hasColumn('programs', 'description')) {
        Schema::table('programs', function (Blueprint $table) {
            $table->text('description')->nullable()->after('quran_price');
        });
    }
}

public function down(): void
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn('description');
    });
    }
};
