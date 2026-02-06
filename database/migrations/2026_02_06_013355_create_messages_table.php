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
    Schema::create('messages', function (Blueprint $table) {
        $table->id();
        $table->string('name');    // Untuk menyimpan nama pengirim
        $table->text('message');  // Untuk menyimpan isi pesan yang panjang
        $table->boolean('is_read')->default(false); // Opsional: Untuk tanda pesan sudah dibaca admin atau belum
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
