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
            // Add talent acceptance fields
            $table->boolean('talent_accepted')->default(false)->after('status');
            $table->timestamp('talent_accepted_at')->nullable()->after('talent_accepted');
            $table->text('talent_acceptance_notes')->nullable()->after('talent_accepted_at');

            // Add admin acceptance fields (separate from approval)
            $table->boolean('admin_accepted')->default(false)->after('talent_acceptance_notes');
            $table->timestamp('admin_accepted_at')->nullable()->after('admin_accepted');
            $table->text('admin_acceptance_notes')->nullable()->after('admin_accepted_at');

            // Add workflow tracking
            $table->boolean('both_parties_accepted')->default(false)->after('admin_acceptance_notes');
            $table->timestamp('workflow_completed_at')->nullable()->after('both_parties_accepted');

            // Index for performance
            $table->index(['talent_accepted', 'admin_accepted']);
            $table->index(['both_parties_accepted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropIndex(['talent_accepted', 'admin_accepted']);
            $table->dropIndex(['both_parties_accepted']);

            $table->dropColumn([
                'talent_accepted',
                'talent_accepted_at',
                'talent_acceptance_notes',
                'admin_accepted',
                'admin_accepted_at',
                'admin_acceptance_notes',
                'both_parties_accepted',
                'workflow_completed_at'
            ]);
        });
    }
};
