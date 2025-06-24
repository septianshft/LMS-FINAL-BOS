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
            // Add talent message when accepting/rejecting request
            $table->text('talent_message')->nullable()->after('recruiter_message');

            // Add admin message when admin processes the request
            $table->text('admin_message')->nullable()->after('talent_message');

            // Add timestamps for when messages were added
            $table->timestamp('talent_message_at')->nullable()->after('talent_message');
            $table->timestamp('admin_message_at')->nullable()->after('admin_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('talent_requests', function (Blueprint $table) {
            $table->dropColumn([
                'talent_message',
                'admin_message',
                'talent_message_at',
                'admin_message_at'
            ]);
        });
    }
};
