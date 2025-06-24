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
        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->boolean('is_hidden_from_trainee')->default(false)->after('passing_score'); // Or choose a suitable position
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_quizzes', function (Blueprint $table) {
            $table->dropColumn('is_hidden_from_trainee');
        });
    }
};
