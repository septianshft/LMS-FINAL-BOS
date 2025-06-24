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
        Schema::table('users', function (Blueprint $table) {
            // Talent scouting opt-in fields
            $table->boolean('available_for_scouting')->default(false)->after('pekerjaan');
            $table->json('talent_skills')->nullable()->after('available_for_scouting');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('talent_skills');
            $table->text('talent_bio')->nullable()->after('hourly_rate');
            $table->string('portfolio_url')->nullable()->after('talent_bio');
            $table->string('location')->nullable()->after('portfolio_url');
            $table->string('phone')->nullable()->after('location');
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable()->after('phone');
            $table->boolean('is_active_talent')->default(true)->after('experience_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'available_for_scouting',
                'talent_skills',
                'hourly_rate',
                'talent_bio',
                'portfolio_url',
                'location',
                'phone',
                'experience_level',
                'is_active_talent'
            ]);
        });
    }
};
