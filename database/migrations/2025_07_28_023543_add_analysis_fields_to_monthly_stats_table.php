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
        Schema::table('monthly_stats', function (Blueprint $table) {
            // Weekly average mood (average of weekly averages)
            $table->float('avg_weekly_mood')->nullable()->after('avg_mood');
            
            // Mood fluctuation (standard deviation)
            $table->float('mood_fluctuation')->nullable()->after('avg_weekly_mood');
            
            // Good mood days count
            $table->integer('good_mood_days')->default(0)->after('mood_fluctuation');
            
            // Low mood days count
            $table->integer('low_mood_days')->default(0)->after('good_mood_days');
            
            // Most frequent mood
            $table->integer('most_frequent_mood')->nullable()->after('low_mood_days');
            
            // Activity analysis (JSON)
            $table->json('activity_analysis')->nullable()->after('most_frequent_mood');
            
            // Productivity analysis (JSON)
            $table->json('productivity_analysis')->nullable()->after('activity_analysis');
            
            // Monthly pattern analysis (JSON)
            $table->json('pattern_analysis')->nullable()->after('productivity_analysis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_stats', function (Blueprint $table) {
            $table->dropColumn([
                'avg_weekly_mood',
                'mood_fluctuation',
                'good_mood_days',
                'low_mood_days',
                'most_frequent_mood',
                'activity_analysis',
                'productivity_analysis',
                'pattern_analysis'
            ]);
        });
    }
};
