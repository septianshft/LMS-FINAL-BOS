<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('course_mode_id')->nullable()->constrained('course_modes');
            $table->foreignId('course_level_id')->nullable()->constrained('course_levels');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_mode_id');
            $table->dropConstrainedForeignId('course_level_id');
        });
    }
};
