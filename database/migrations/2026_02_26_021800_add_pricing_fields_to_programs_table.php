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
    Schema::table('programs', function (Blueprint $table) {
        // Harga per pertemuan tambahan (misal: 50.000)
        $table->decimal('extra_price', 15, 2)->default(0)->after('price'); 
        
        // Harga khusus untuk paket mengaji (jika dipilih)
        $table->decimal('quran_price', 15, 2)->default(0)->after('extra_price');
    });
}

public function down(): void
{
    Schema::table('programs', function (Blueprint $table) {
        $table->dropColumn(['extra_price', 'quran_price']);
    });
}
};
