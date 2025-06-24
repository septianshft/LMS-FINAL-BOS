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
            // Add project relationship for backward compatibility
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->index('project_id');

            // Add fields to track migration status
            $table->boolean('migrated_to_project')->default(false);
            $table->timestamp('migrated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex(['project_id']);
            $table->dropColumn(['project_id', 'migrated_to_project', 'migrated_at']);
        });
    }
};
