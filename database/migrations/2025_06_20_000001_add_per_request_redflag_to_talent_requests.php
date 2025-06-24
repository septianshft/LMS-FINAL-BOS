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
            $table->boolean('is_redflagged')->default(false)->after('status');
            $table->text('redflag_reason')->nullable()->after('is_redflagged');
            $table->timestamp('redflagged_at')->nullable()->after('redflag_reason');
            $table->unsignedBigInteger('redflagged_by')->nullable()->after('redflagged_at');

            // Add foreign key constraint for redflagged_by (admin user)
            $table->foreign('redflagged_by')->references('id')->on('users')->onDelete('set null');

            // Add index for performance
            $table->index(['talent_id', 'is_redflagged']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropForeign(['redflagged_by']);
            $table->dropIndex(['talent_id', 'is_redflagged']);
            $table->dropColumn(['is_redflagged', 'redflag_reason', 'redflagged_at', 'redflagged_by']);
        });
    }
};
