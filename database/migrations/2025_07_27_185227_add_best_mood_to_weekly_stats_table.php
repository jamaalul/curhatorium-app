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
        Schema::table('weekly_stats', function (Blueprint $table) {
            $table->float('best_mood')->nullable()->after('total_entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_stats', function (Blueprint $table) {
            $table->dropColumn('best_mood');
        });
    }
};
