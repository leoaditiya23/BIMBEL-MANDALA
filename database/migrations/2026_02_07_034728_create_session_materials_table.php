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
    Schema::create('session_materials', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('program_id');
        $table->integer('session_number'); // Sesi 1, 2, dst
        $table->string('title');
        $table->text('content_description')->nullable();
        $table->string('video_link')->nullable();
        $table->string('file_path')->nullable();
        $table->timestamps();

        $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_materials');
    }
};
