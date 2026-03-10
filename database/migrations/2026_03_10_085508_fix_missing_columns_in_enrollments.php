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
        if (!Schema::hasColumn('enrollments', 'jumlah_pertemuan')) {
            $table->integer('jumlah_pertemuan')->default(0)->after('metode');
        }
        if (!Schema::hasColumn('enrollments', 'ambil_mengaji')) {
            $table->string('ambil_mengaji')->default('tidak')->after('jumlah_pertemuan');
        }
    });
}

public function down(): void
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropColumn(['jumlah_pertemuan', 'ambil_mengaji']);
    });
}
};
