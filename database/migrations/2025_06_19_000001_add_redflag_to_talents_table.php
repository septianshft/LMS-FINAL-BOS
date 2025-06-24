<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->boolean('redflagged')->default(false)->after('is_active');
            $table->text('redflag_reason')->nullable()->after('redflagged');
        });
    }

    public function down()
    {
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['redflagged', 'redflag_reason']);
        });
    }
};
