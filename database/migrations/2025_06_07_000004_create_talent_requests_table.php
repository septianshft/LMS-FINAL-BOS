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
        Schema::create('talent_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruiter_id')->constrained('recruiters')->onDelete('cascade');
            $table->foreignId('talent_id')->constrained('talents')->onDelete('cascade');
            $table->string('project_title');
            $table->text('project_description');
            $table->text('requirements')->nullable();
            $table->string('budget_range')->nullable();
            $table->string('project_duration')->nullable();
            $table->enum('urgency_level', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'meeting_arranged', 'agreement_reached', 'onboarded', 'rejected', 'completed'])->default('pending');
            $table->text('recruiter_message')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('meeting_arranged_at')->nullable();
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talent_requests');
    }
};
