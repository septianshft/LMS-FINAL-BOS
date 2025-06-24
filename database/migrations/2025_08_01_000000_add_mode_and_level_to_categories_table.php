<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('course_type', ['online', 'onsite'])->default('online');
            $table->enum('level', ['beginner', 'intermediate', 'advance'])->default('beginner');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['course_type', 'level']);
        });
    }
};
