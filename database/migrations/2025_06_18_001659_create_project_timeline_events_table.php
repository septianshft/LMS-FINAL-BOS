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
        Schema::create('project_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->enum('event_type', [
                'created', 'approved', 'talent_assigned', 'talent_accepted', 'talent_rejected',
                'extension_requested', 'extended', 'overdue', 'closure_requested', 'completed',
                'conflict_detected', 'conflict_resolved', 'notification_sent'
            ]);
            $table->text('event_description');
            $table->foreignId('triggered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('event_data')->nullable(); // Store additional context data
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['project_id', 'created_at']);
            $table->index('event_type');
            $table->index('triggered_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timeline_events');
    }
};
