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
            if (!Schema::hasColumn('enrollments', 'payment_method')) {
                $table->string('payment_method', 20)->default('manual')->after('status_pembayaran');
            }

            if (!Schema::hasColumn('enrollments', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->after('payment_method')->unique();
            }

            if (!Schema::hasColumn('enrollments', 'midtrans_snap_token')) {
                $table->string('midtrans_snap_token')->nullable()->after('midtrans_order_id');
            }

            if (!Schema::hasColumn('enrollments', 'midtrans_transaction_status')) {
                $table->string('midtrans_transaction_status', 50)->nullable()->after('midtrans_snap_token');
            }

            if (!Schema::hasColumn('enrollments', 'midtrans_payment_type')) {
                $table->string('midtrans_payment_type', 50)->nullable()->after('midtrans_transaction_status');
            }

            if (!Schema::hasColumn('enrollments', 'midtrans_payload')) {
                $table->longText('midtrans_payload')->nullable()->after('midtrans_payment_type');
            }

            if (!Schema::hasColumn('enrollments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('midtrans_payload');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'midtrans_order_id')) {
                $table->dropUnique('enrollments_midtrans_order_id_unique');
            }

            $columns = [
                'payment_method',
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_transaction_status',
                'midtrans_payment_type',
                'midtrans_payload',
                'paid_at',
            ];

            $dropColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('enrollments', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
