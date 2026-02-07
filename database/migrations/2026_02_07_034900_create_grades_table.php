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
    Schema::create('grades', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('program_id');
        $table->unsignedBigInteger('student_id');
        $table->unsignedBigInteger('mentor_id');
        $table->string('title'); // Contoh: "Ujian Tengah Semester" atau "Tugas Aljabar"
        $table->integer('score');
        $table->text('mentor_note')->nullable();
        $table->timestamps();

        $table->foreign('program_id')->references('id')->on('programs');
        $table->foreign('student_id')->references('id')->on('users');
        $table->foreign('mentor_id')->references('id')->on('users');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
