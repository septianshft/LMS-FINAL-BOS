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
        Schema::table('talent_requests', function (Blueprint $table) {
            // Remove only the messaging fields that actually exist
            if (Schema::hasColumn('talent_requests', 'recruiter_message')) {
                $table->dropColumn('recruiter_message');
            }
            if (Schema::hasColumn('talent_requests', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            if (Schema::hasColumn('talent_requests', 'talent_acceptance_notes')) {
                $table->dropColumn('talent_acceptance_notes');
            }
            if (Schema::hasColumn('talent_requests', 'admin_acceptance_notes')) {
                $table->dropColumn('admin_acceptance_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            // Restore messaging fields if rollback needed
            $table->text('recruiter_message')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('talent_acceptance_notes')->nullable();
            $table->text('admin_acceptance_notes')->nullable();
        });
    }
};
