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
        Schema::table('project_timeline_events', function (Blueprint $table) {
            // Add new event types to enum
            $table->dropColumn('event_type');
        });

        Schema::table('project_timeline_events', function (Blueprint $table) {
            $table->enum('event_type', [
                'created', 'approved', 'rejected', 'talent_assigned', 'talent_accepted', 'talent_rejected',
                'extension_requested', 'extension_approved', 'extension_rejected', 'extended', 'overdue',
                'closure_requested', 'completed', 'conflict_detected', 'conflict_resolved', 'notification_sent'
            ])->after('project_id');

            // Rename columns to match model
            $table->renameColumn('event_description', 'description');
            $table->renameColumn('triggered_by', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_timeline_events', function (Blueprint $table) {
            $table->renameColumn('description', 'event_description');
            $table->renameColumn('user_id', 'triggered_by');

            $table->dropColumn('event_type');
        });

        Schema::table('project_timeline_events', function (Blueprint $table) {
            $table->enum('event_type', [
                'created', 'approved', 'talent_assigned', 'talent_accepted', 'talent_rejected',
                'extension_requested', 'extended', 'overdue', 'closure_requested', 'completed',
                'conflict_detected', 'conflict_resolved', 'notification_sent'
            ])->after('project_id');
        });
    }
};
