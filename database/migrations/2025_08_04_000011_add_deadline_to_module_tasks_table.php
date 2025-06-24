<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_tasks', function (Blueprint $table) {
            $table->timestamp('deadline')->nullable()->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('module_tasks', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
};
