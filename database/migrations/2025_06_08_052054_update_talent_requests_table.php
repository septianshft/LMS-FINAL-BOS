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
            // Add direct reference to user instead of talent
            $table->foreignId('talent_user_id')->nullable()->after('talent_id')->constrained('users')->onDelete('cascade');

            // Add index for better performance
            $table->index(['talent_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropForeign(['talent_user_id']);
            $table->dropIndex(['talent_user_id', 'status']);
            $table->dropColumn('talent_user_id');
        });
    }
};
