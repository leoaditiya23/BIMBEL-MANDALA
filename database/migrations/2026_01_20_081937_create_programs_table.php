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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Reguler UTBK, Intensif Kedokteran
            $table->string('type'); // reguler, intensif, privat
            $table->integer('price');
            $table->text('description')->nullable(); // Ditambah nullable agar opsional
            
            // TAMBAHAN: Kolom Mentor (Relasi ke tabel users)
            // Menggunakan foreignId agar bisa di-join di PageController
            $table->foreignId('mentor_id')
                  ->nullable() 
                  ->constrained('users')
                  ->onDelete('set null'); // Jika mentor dihapus, program tetap ada tapi mentor kosong
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};