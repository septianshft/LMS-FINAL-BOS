<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_progresses', function (Blueprint $table) {
            $table->json('completed_materials')->nullable()->after('completed_videos');
            $table->json('completed_tasks')->nullable()->after('completed_materials');
        });
    }

    public function down(): void
    {
        Schema::table('course_progresses', function (Blueprint $table) {
            $table->dropColumn(['completed_materials', 'completed_tasks']);
        });
    }
};
