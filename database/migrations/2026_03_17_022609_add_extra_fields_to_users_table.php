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
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'birth_date')) {
            $table->date('birth_date')->nullable()->after('password');
        }
        if (!Schema::hasColumn('users', 'gender')) {
            $table->string('gender')->nullable()->after('birth_date');
        }
        // Kolom whatsapp dilewati jika sudah ada, atau tambahkan pengecekan ini:
        if (!Schema::hasColumn('users', 'whatsapp')) {
            $table->string('whatsapp')->nullable()->after('gender');
        }
        if (!Schema::hasColumn('users', 'school')) {
            $table->string('school')->nullable()->after('whatsapp');
        }
        if (!Schema::hasColumn('users', 'referral')) {
            $table->string('referral')->nullable()->after('school');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
