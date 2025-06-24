<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->date('enrollment_start')->nullable()->after('course_level_id');
            $table->date('enrollment_end')->nullable()->after('enrollment_start');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['enrollment_start', 'enrollment_end']);
        });
    }
};
