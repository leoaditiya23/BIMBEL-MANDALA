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
        // 1. Update Tabel Users untuk kebutuhan Registrasi & Trial
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'jenjang')) {
                $table->enum('jenjang', ['TK', 'SD', 'SMP', 'SMA'])->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('users', 'is_trial_claimed')) {
                $table->boolean('is_trial_claimed')->default(false)->after('role');
            }
        });

        // 2. Update Tabel Enrollments untuk alur Program & Pembayaran
        Schema::table('enrollments', function (Blueprint $table) {
            // Field untuk Metode Belajar & Lokasi (Reguler Offline)
            if (!Schema::hasColumn('enrollments', 'metode')) {
                $table->enum('metode', ['online', 'offline'])->default('online')->after('program_id');
            }
            if (!Schema::hasColumn('enrollments', 'alamat_semarang')) {
                $table->string('alamat_semarang')->nullable()->after('metode');
            }

            // Field untuk Opsi Bimbingan Mengaji (Reguler Offline)
            if (!Schema::hasColumn('enrollments', 'ambil_mengaji')) {
                $table->boolean('ambil_mengaji')->default(false)->after('alamat_semarang');
            }

            // Field untuk Verifikasi Pembayaran oleh Admin
            if (!Schema::hasColumn('enrollments', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['pending', 'verified', 'rejected'])->default('pending')->after('ambil_mengaji');
            }

            // Field untuk Monitoring Progres oleh Mentor
            if (!Schema::hasColumn('enrollments', 'progres_nilai')) {
                $table->integer('progres_nilai')->default(0)->after('status_pembayaran');
            }
            if (!Schema::hasColumn('enrollments', 'catatan_mentor')) {
                $table->text('catatan_mentor')->nullable()->after('progres_nilai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'jenjang', 'is_trial_claimed']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'metode', 
                'alamat_semarang', 
                'ambil_mengaji', 
                'status_pembayaran', 
                'progres_nilai', 
                'catatan_mentor'
            ]);
        });
    }
};