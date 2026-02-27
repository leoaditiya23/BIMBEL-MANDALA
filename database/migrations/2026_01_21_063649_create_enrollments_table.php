<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            // Relasi Utama
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            
            // Logika Berdasarkan Diagram Alur
            $table->enum('metode', ['online', 'offline'])->default('online');
            
            // Kolom untuk membedakan materi (TKA atau UTBK saja)
            $table->string('sub_type')->nullable(); 
            
            // Kolom untuk Reguler Online (Fokus Jenjang)
            $table->string('jenjang')->nullable(); // TK, SD, SMP, SMA
            
            // Kolom untuk Reguler Offline (Mapel, Lokasi, Jadwal)
            $table->string('mapel')->nullable();
            $table->string('alamat_semarang')->nullable();
            $table->string('jadwal_pertemuan')->nullable();
            
            // --- REVISI: FITUR DINAMIS & TAMBAH JAM ---
            $table->boolean('ambil_mengaji')->default(false);
            $table->integer('extra_meetings_count')->default(0); // Menyimpan jumlah jam/pertemuan tambahan
            
            // Menyimpan "Snapshot" harga saat pendaftaran (agar laporan keuangan tidak berubah jika admin ganti harga di masa depan)
            $table->integer('base_price_at_enroll')->default(0); 
            $table->integer('extra_price_at_enroll')->default(0); 
            $table->integer('quran_price_at_enroll')->default(0); 
            // ------------------------------------------

            // Kolom untuk Kelas Intensif (Pilihan Batch)
            $table->string('batch_intensif')->nullable(); 
            
            // Status & Keuangan
            $table->integer('total_harga')->default(0); 
            $table->integer('payment_code')->nullable(); // Untuk menyimpan kode unik (misal 3 angka terakhir WA)
            
            // Kolom untuk menyimpan nama file bukti transfer
            $table->string('bukti_pembayaran')->nullable(); 
            
            $table->enum('status_pembayaran', ['pending', 'verified'])->default('pending');
            $table->integer('progres_nilai')->default(0); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('enrollments');
    }
};