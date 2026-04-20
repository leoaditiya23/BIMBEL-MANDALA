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
            if (!Schema::hasColumn('enrollments', 'mentor_assignment_status')) {
                $table->enum('mentor_assignment_status', ['approved', 'pending', 'rejected'])
                    ->default('approved')
                    ->after('mentor_id');
            }

            if (!Schema::hasColumn('enrollments', 'mentor_assignment_note')) {
                $table->string('mentor_assignment_note', 255)
                    ->nullable()
                    ->after('mentor_assignment_status');
            }

            if (!Schema::hasColumn('enrollments', 'mentor_requested_at')) {
                $table->timestamp('mentor_requested_at')
                    ->nullable()
                    ->after('mentor_assignment_note');
            }

            if (!Schema::hasColumn('enrollments', 'mentor_responded_at')) {
                $table->timestamp('mentor_responded_at')
                    ->nullable()
                    ->after('mentor_requested_at');
            }

            if (!Schema::hasColumn('enrollments', 'assigned_by_admin_id')) {
                $table->unsignedBigInteger('assigned_by_admin_id')
                    ->nullable()
                    ->after('mentor_responded_at');
                $table->foreign('assigned_by_admin_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'assigned_by_admin_id')) {
                $table->dropForeign(['assigned_by_admin_id']);
                $table->dropColumn('assigned_by_admin_id');
            }

            $dropColumns = [];
            foreach (['mentor_responded_at', 'mentor_requested_at', 'mentor_assignment_note', 'mentor_assignment_status'] as $column) {
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
