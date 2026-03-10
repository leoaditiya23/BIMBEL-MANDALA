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
    // Tabel Materi
    Schema::create('materis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
        $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->string('file_path')->nullable(); // Untuk PDF/Link
        $table->timestamps();
    });

    // Tabel Tugas
    Schema::create('tugas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
        $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
        $table->string('judul');
        $table->text('instruksi');
        $table->dateTime('deadline');
        $table->timestamps();
    });

    // Tabel Absensi
    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
        $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha']);
        $table->date('tanggal');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akademik_tables');
    }
};
