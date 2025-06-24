<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes for talent request system optimization
     */
    public function up(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            // Index for recruiter dashboard queries (status filtering + ordering)
            $table->index(['recruiter_id', 'status', 'created_at'], 'idx_recruiter_status_date');

            // Index for talent admin analytics queries
            $table->index(['status', 'created_at'], 'idx_status_analytics');

            // Index for talent availability checks (using talent_user_id)
            $table->index(['talent_user_id', 'is_blocking_talent', 'project_end_date'], 'idx_talent_availability');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for talent discovery searches
            $table->index(['available_for_scouting', 'is_active_talent', 'updated_at'], 'idx_talent_discovery');

            // Index for talent skills searches (JSON queries)
            $table->index(['is_active_talent', 'available_for_scouting'], 'idx_talent_active_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropIndex('idx_recruiter_status_date');
            $table->dropIndex('idx_status_analytics');
            $table->dropIndex('idx_talent_availability');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_talent_discovery');
            $table->dropIndex('idx_talent_active_available');
        });
    }
};
