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
    Schema::table('enrollments', function (Blueprint $table) {
        $columns = [
            'tipe_paket' => ['type' => 'string', 'after' => 'jenjang'],
            'per_minggu' => ['type' => 'integer', 'after' => 'tipe_paket', 'default' => 1],
            'extra_hours' => ['type' => 'integer', 'after' => 'per_minggu', 'default' => 0],
            'is_mengaji' => ['type' => 'boolean', 'after' => 'extra_hours', 'default' => false],
            'jadwal_detail' => ['type' => 'text', 'after' => 'is_mengaji'],
            'mapel' => ['type' => 'text', 'after' => 'jadwal_detail'],
            'metode' => ['type' => 'string', 'after' => 'mapel'],
            'lokasi_cabang' => ['type' => 'string', 'after' => 'metode'],
            'alamat_siswa' => ['type' => 'text', 'after' => 'lokasi_cabang'],
        ];

        foreach ($columns as $name => $attr) {
            if (!Schema::hasColumn('enrollments', $name)) {
                $column = $table->{$attr['type']}($name)->nullable();
                if (isset($attr['after'])) $column->after($attr['after']);
                if (isset($attr['default'])) $column->default($attr['default']);
            }
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
        });
    }
};
