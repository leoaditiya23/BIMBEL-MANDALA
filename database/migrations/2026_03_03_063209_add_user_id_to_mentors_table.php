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
        Schema::table('mentors', function (Blueprint $row) {
            // Menambahkan kolom user_id sebagai foreign key
            if (!Schema::hasColumn('mentors', 'user_id')) {
                $row->unsignedBigInteger('user_id')->nullable()->after('id');
                
                // Jika ingin otomatis terhapus saat user dihapus:
                $row->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $row) {
            $row->dropForeign(['user_id']);
            $row->dropColumn('user_id');
        });
    }
};