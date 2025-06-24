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
        Schema::create('project_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('conflict_id')->nullable()->constrained('timeline_conflicts')->onDelete('set null');

            // Extension Details
            $table->date('original_end_date');
            $table->date('requested_end_date');
            $table->integer('extension_days');
            $table->text('extension_reason');
            $table->decimal('additional_budget', 10, 2)->default(0.00);

            // Request Information
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('requested_at')->useCurrent();
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('triggered_by_talent_addition')->default(false);

            // Approval Workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Impact Assessment
            $table->json('affected_assignments')->nullable(); // Store array of affected talent IDs
            $table->boolean('stakeholder_notifications_sent')->default(false);
            $table->text('business_justification')->nullable();
            $table->boolean('client_approval_required')->default(false);
            $table->boolean('client_approved')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['status', 'requested_at']);
            $table->index(['project_id', 'status']);
            $table->index(['status', 'urgency_level', 'requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_extensions');
    }
};
