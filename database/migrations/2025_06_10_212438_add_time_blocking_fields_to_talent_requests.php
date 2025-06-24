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
            // Time-blocking fields for talent availability management
            $table->datetime('project_start_date')->nullable()->after('project_duration');
            $table->datetime('project_end_date')->nullable()->after('project_start_date');
            $table->boolean('is_blocking_talent')->default(false)->after('project_end_date');
            $table->text('blocking_notes')->nullable()->after('is_blocking_talent');

            // Index for performance when checking talent availability
            $table->index(['talent_id', 'is_blocking_talent', 'project_end_date'], 'talent_blocking_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropIndex('talent_blocking_index');
            $table->dropColumn([
                'project_start_date',
                'project_end_date',
                'is_blocking_talent',
                'blocking_notes'
            ]);
        });
    }
};
