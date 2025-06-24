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
            // Remove messaging-related columns if they exist
            if (Schema::hasColumn('talent_requests', 'message_thread_id')) {
                $table->dropColumn('message_thread_id');
            }
            if (Schema::hasColumn('talent_requests', 'last_message_at')) {
                $table->dropColumn('last_message_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->string('message_thread_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
        });
    }
};
