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
    Schema::table('assignments', function (Blueprint $table) {
        // Tambahkan kolom student_id setelah mentor_id
        $table->unsignedBigInteger('student_id')->nullable()->after('mentor_id');
        
        // Opsional: Buat foreign key agar data konsisten
        $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('assignments', function (Blueprint $table) {
        $table->dropForeign(['student_id']);
        $table->dropColumn('student_id');
    });
}
};
