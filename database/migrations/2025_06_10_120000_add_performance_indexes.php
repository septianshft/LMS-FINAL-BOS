<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check existing indexes to avoid conflicts
        $talentRequestIndexes = DB::select("SHOW INDEX FROM talent_requests");
        $existingIndexes = array_column($talentRequestIndexes, 'Key_name');

        // Add performance indexes for talent_requests table only if they don't exist
        Schema::table('talent_requests', function (Blueprint $table) use ($existingIndexes) {
            // Index for analytics time-based queries (unique to this migration)
            if (!in_array('idx_analytics_timeframe', $existingIndexes)) {
                $table->index(['created_at', 'status'], 'idx_analytics_timeframe');
            }

            // Index for workflow state queries (unique to this migration)
            if (!in_array('idx_workflow_state', $existingIndexes)) {
                $table->index(['talent_accepted', 'admin_accepted', 'both_parties_accepted'], 'idx_workflow_state');
            }
        });

        // Check existing user table indexes
        $userIndexes = DB::select("SHOW INDEX FROM users");
        $existingUserIndexes = array_column($userIndexes, 'Key_name');

        // Add performance indexes for users table (talent search) only if they don't exist
        Schema::table('users', function (Blueprint $table) use ($existingUserIndexes) {
            // Index for experience level filtering (unique to this migration)
            if (!in_array('idx_experience_search', $existingUserIndexes)) {
                $table->index(['experience_level', 'available_for_scouting'], 'idx_experience_search');
            }

            // Index for hourly rate searches (unique to this migration)
            if (!in_array('idx_hourly_rate_search', $existingUserIndexes)) {
                $table->index(['hourly_rate', 'is_active_talent'], 'idx_hourly_rate_search');
            }
        });

        // Create optimized view for dashboard analytics (safe approach)
        DB::statement('
            CREATE OR REPLACE VIEW talent_request_analytics_view AS
            SELECT
                DATE(created_at) as date,
                status,
                COUNT(*) as request_count,
                COUNT(DISTINCT talent_user_id) as unique_talents,
                COUNT(DISTINCT recruiter_id) as unique_recruiters,
                AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_processing_time_hours
            FROM talent_requests
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            AND deleted_at IS NULL
            GROUP BY DATE(created_at), status
        ');
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop custom indexes safely
        Schema::table('talent_requests', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_analytics_timeframe');
            } catch (Exception $e) {
                // Index may not exist
            }
            try {
                $table->dropIndex('idx_urgent_requests');
            } catch (Exception $e) {
                // Index may not exist
            }
            try {
                $table->dropIndex('idx_workflow_state');
            } catch (Exception $e) {
                // Index may not exist
            }
        });

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_experience_search');
            } catch (Exception $e) {
                // Index may not exist
            }
            try {
                $table->dropIndex('idx_hourly_rate_search');
            } catch (Exception $e) {
                // Index may not exist
            }
        });

        // Drop the analytics view
        DB::statement('DROP VIEW IF EXISTS talent_request_analytics_view');
    }
};
