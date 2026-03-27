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
    Schema::table('program_materials', function (Blueprint $table) {
        // Menambahkan kolom quiz_url setelah kolom video_url
        $table->text('quiz_url')->nullable()->after('video_url');
    });
}

public function down(): void
{
    Schema::table('program_materials', function (Blueprint $table) {
        $table->dropColumn('quiz_url');
    });
}
};
