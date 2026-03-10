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
    Schema::table('enrollments', function (Blueprint $table) {
        // Menambahkan kolom yang dibutuhkan
        $table->string('hari')->nullable()->after('mapel');
        $table->string('jam_mulai')->nullable()->after('hari');
        $table->unsignedBigInteger('mentor_id')->nullable()->after('jam_mulai');
        
        // Optional: Jika ingin buat relasi ke tabel users (mentor)
        $table->foreign('mentor_id')->references('id')->on('users')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropForeign(['mentor_id']);
        $table->dropColumn(['hari', 'jam_mulai', 'mentor_id']);
    });
}
};
