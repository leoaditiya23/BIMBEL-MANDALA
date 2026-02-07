<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'specialization')) {
            $table->string('specialization')->nullable()->after('email');
        }
        if (!Schema::hasColumn('users', 'whatsapp')) {
            $table->string('whatsapp')->nullable()->after('specialization');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ini untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['specialization', 'whatsapp']);
        });
    }
};