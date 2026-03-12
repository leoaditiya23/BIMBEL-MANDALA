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
        // Menambahkan kolom is_absen_active dengan default 0 (false)
        $table->boolean('is_absen_active')->default(false)->after('hari');
    });
}

public function down(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropColumn('is_absen_active');
    });
}
};
