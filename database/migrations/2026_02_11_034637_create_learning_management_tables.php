<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Materi / Sesi
        Schema::create('program_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->integer('session_number')->comment('Pertemuan ke-X');
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Absensi
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['hadir', 'izin', 'alfa'])->default('hadir');
            $table->timestamps();
        });

        // 3. Tabel Nilai
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('title'); // Nama tugas/ujian
            $table->integer('score');
            $table->text('note')->nullable(); // Feedback mentor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('program_materials');
    }
};