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
    Schema::table('task_submissions', function (Blueprint $table) {
        // Tambahkan kolom yang dibutuhkan tanpa mempedulikan urutan 'after'
        if (!Schema::hasColumn('task_submissions', 'link')) {
            $table->string('link')->nullable();
        }
        if (!Schema::hasColumn('task_submissions', 'score')) {
            $table->integer('score')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            //
        });
    }
};
