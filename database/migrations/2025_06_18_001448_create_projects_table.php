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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('industry')->nullable();
            $table->text('general_requirements')->nullable();

            // Budget & Timeline
            $table->decimal('overall_budget_min', 10, 2)->nullable();
            $table->decimal('overall_budget_max', 10, 2)->nullable();
            $table->date('expected_start_date');
            $table->date('expected_end_date');
            $table->integer('estimated_duration_days');

            // Status Management
            $table->enum('status', [
                'draft', 'pending_admin', 'approved', 'active', 'overdue',
                'extension_requested', 'closure_requested', 'completed', 'cancelled'
            ])->default('draft');

            // Ownership & Approval
            $table->foreignId('recruiter_id')->constrained('recruiters')->onDelete('cascade');
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_approved_at')->nullable();

            // Extension Management
            $table->date('requested_end_date')->nullable();
            $table->text('extension_reason')->nullable();
            $table->decimal('additional_budget', 10, 2)->nullable();
            $table->timestamp('extension_requested_at')->nullable();
            $table->integer('days_overdue')->default(0);
            $table->date('overdue_since')->nullable();
            $table->boolean('auto_extended')->default(false);
            $table->boolean('grace_period_used')->default(false);

            // Closure Management
            $table->timestamp('closure_requested_at')->nullable();
            $table->foreignId('closure_requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('closure_reason')->nullable();
            $table->timestamp('closure_approved_at')->nullable();
            $table->foreignId('closure_approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('recruiter_id');
            $table->index('expected_end_date');
            $table->index(['status', 'expected_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
