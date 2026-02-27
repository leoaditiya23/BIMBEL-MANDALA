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
    Schema::table('mentors', function (Blueprint $table) {
        // Menambahkan kolom whatsapp setelah kolom specialist
        $table->string('whatsapp')->nullable()->after('specialist');
    });
}

public function down(): void
{
    Schema::table('mentors', function (Blueprint $table) {
        $table->dropColumn('whatsapp');
    });
}
};
