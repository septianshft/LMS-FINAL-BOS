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
        Schema::create('timeline_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('assignment_id')->nullable()->constrained('project_assignments')->onDelete('set null');

            // Conflict Details
            $table->enum('conflict_type', [
                'duration_overrun', 'late_addition', 'availability_clash', 'budget_mismatch'
            ]);
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->timestamp('detected_at')->useCurrent();

            // Timeline Information
            $table->date('project_end_date');
            $table->date('talent_proposed_end_date');
            $table->integer('overrun_days');
            $table->decimal('overrun_percentage', 5, 2);

            // Resolution Tracking
            $table->enum('resolution_status', [
                'detected', 'analyzing', 'resolved', 'escalated'
            ])->default('detected');
            $table->enum('resolution_strategy', [
                'reduce_duration', 'extend_project', 'split_project', 'replace_talent', 'approved_overrun'
            ])->nullable();
            $table->timestamp('resolution_applied_at')->nullable();
            $table->text('resolution_notes')->nullable();

            // Impact Assessment
            $table->integer('affected_talents_count')->default(0);
            $table->decimal('budget_impact', 10, 2)->default(0.00);
            $table->integer('timeline_impact_days')->default(0);
            $table->enum('business_risk_level', [
                'minimal', 'low', 'medium', 'high', 'critical'
            ])->default('minimal');

            // Approval & Resolution
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('admin_approved')->default(false);
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_approved_at')->nullable();
            $table->boolean('auto_resolved')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['resolution_status', 'severity']);
            $table->index(['project_id', 'detected_at']);
            $table->index('conflict_type');
            $table->index(['overrun_days', 'overrun_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_conflicts');
    }
};
