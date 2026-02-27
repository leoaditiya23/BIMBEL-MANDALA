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
            $table->string('jenjang')->nullable(); // SD, SMP, SMA, Umum
            
            // --- HARGA UTAMA ---
            $table->integer('price')->default(0); 

            // --- FITUR DINAMIS BARU (ADMIN BISA UBAH LEWAT DB) ---
            // Biaya per satu kali pertemuan tambahan (misal: 50000)
            $table->integer('extra_meeting_price')->default(0); 
            
            // Biaya tambahan jika siswa mengambil paket mengaji (misal: 30000)
            $table->integer('quran_price')->default(0); 
            // -----------------------------------------------------

            $table->text('description')->nullable();
            
            // Relasi ke tabel users (Mentor)
            $table->foreignId('mentor_id')
                  ->nullable() 
                  ->constrained('users')
                  ->onDelete('set null'); 
            
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