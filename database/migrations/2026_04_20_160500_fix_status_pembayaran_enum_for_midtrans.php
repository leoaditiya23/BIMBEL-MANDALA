<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `enrollments` MODIFY COLUMN `status_pembayaran` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE `enrollments` SET `status_pembayaran` = 'pending' WHERE `status_pembayaran` = 'rejected'");
        DB::statement("ALTER TABLE `enrollments` MODIFY COLUMN `status_pembayaran` ENUM('pending','verified') NOT NULL DEFAULT 'pending'");
    }
};
