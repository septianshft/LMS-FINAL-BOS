<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'course_type')) {
                $table->dropColumn('course_type');
            }
            if (Schema::hasColumn('categories', 'level')) {
                $table->dropColumn('level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('course_type', ['online', 'onsite'])->default('online');
            $table->enum('level', ['beginner', 'intermediate', 'advance'])->default('beginner');
        });
    }
};
