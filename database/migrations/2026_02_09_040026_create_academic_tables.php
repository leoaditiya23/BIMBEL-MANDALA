<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Absensi
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('status')->default(0); 
                $table->timestamps();
            });
        }

        // 2. Tabel Tugas (Hanya buat jika belum ada)
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('link')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Pengumpulan Tugas
        if (!Schema::hasTable('task_submissions')) {
            Schema::create('task_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 4. Tabel Nilai
        if (!Schema::hasTable('grades')) {
            Schema::create('grades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('program_id')->nullable();
                $table->foreignId('mentor_id')->nullable();
                $table->string('title');
                $table->integer('score');
                $table->text('mentor_note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Berhati-hati saat rollback, jangan hapus tabel yang sudah ada sebelumnya jika penting
        Schema::dropIfExists('grades');
        Schema::dropIfExists('task_submissions');
        // Schema::dropIfExists('assignments'); // Komentari ini jika assignments punya data penting
        Schema::dropIfExists('attendances');
    }
};