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
        Schema::table('recruiters', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('user_id');
            $table->string('industry')->nullable()->after('company_name');
            $table->string('company_size')->nullable()->after('industry');
            $table->string('website')->nullable()->after('company_size');
            $table->text('company_description')->nullable()->after('website');
            $table->string('phone')->nullable()->after('company_description');
            $table->text('address')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruiters', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'industry',
                'company_size',
                'website',
                'company_description',
                'phone',
                'address'
            ]);
        });
    }
};
