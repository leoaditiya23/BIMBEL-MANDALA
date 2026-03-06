<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // Tambahkan baris if ini:
    if (!Schema::hasTable('subjects')) {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('jenjang');
            $table->timestamps();
        });
    }
}

    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['per_minggu', 'extra_hours', 'jadwal_detail', 'ambil_mengaji', 'bukti_pembayaran']);
        });
    }
};