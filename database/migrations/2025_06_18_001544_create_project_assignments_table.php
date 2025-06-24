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
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('talent_id')->constrained('talents')->onDelete('cascade');

            // Individual Assignment Details
            $table->string('specific_role');
            $table->date('talent_start_date');
            $table->date('talent_end_date');
            $table->decimal('individual_budget', 10, 2)->nullable();
            $table->text('specific_requirements')->nullable();
            $table->integer('working_hours_per_week')->default(40);
            $table->enum('priority_level', ['low', 'medium', 'high'])->default('medium');
            $table->text('assignment_notes')->nullable();

            // Status Management
            $table->enum('status', [
                'assigned', 'admin_pending', 'talent_pending', 'accepted', 'active', 'completed'
            ])->default('assigned');

            // Approval Tracking
            $table->timestamp('admin_approved_at')->nullable();
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('talent_accepted_at')->nullable();
            $table->timestamp('talent_rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Timeline Conflict Management
            $table->boolean('timeline_conflict_detected')->default(false);
            $table->enum('conflict_severity', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->enum('conflict_resolution_strategy', [
                'reduce_duration', 'extend_project', 'split_project', 'replace_talent'
            ])->nullable();
            $table->date('original_end_date')->nullable();
            $table->integer('extension_requests_count')->default(0);
            $table->timestamp('last_timeline_modification')->nullable();
            $table->text('timeline_modification_reason')->nullable();
            $table->boolean('auto_resolution_applied')->default(false);

            // Overrun Protection
            $table->integer('max_allowed_overrun_days')->default(30);
            $table->integer('current_overrun_days')->default(0);
            $table->text('overrun_justification')->nullable();
            $table->foreignId('continuation_project_id')->nullable()->constrained('projects')->onDelete('set null');

            // Performance Tracking
            $table->integer('talent_rating')->nullable()->check('talent_rating >= 1 AND talent_rating <= 5');
            $table->text('performance_notes')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes and Constraints
            $table->index('project_id');
            $table->index('talent_id');
            $table->index('status');
            $table->index(['talent_start_date', 'talent_end_date']);
            $table->unique(['project_id', 'talent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_assignments');
    }
};
