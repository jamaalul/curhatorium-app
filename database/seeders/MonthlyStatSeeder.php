<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyStat;
use App\Models\WeeklyStat;
use App\Models\User;
use Faker\Factory as Faker;

class MonthlyStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;
        
        // Get all weekly stats ordered by week_start
        $weeklyStats = WeeklyStat::orderBy('week_start', 'asc')->get();
        
        // Group weekly stats by month (4 weeks each)
        $monthlyGroups = $weeklyStats->chunk(4);
        
        foreach ($monthlyGroups as $monthStats) {
            if ($monthStats->count() === 0) continue;
            
            $firstWeek = $monthStats->first();
            $monthLabel = $firstWeek->week_start ? \Carbon\Carbon::parse($firstWeek->week_start)->format('F Y') : 'Unknown Month';
            $avgMood = $monthStats->avg('avg_mood');
            $avgProductivity = $monthStats->avg('avg_productivity');
            $bestMood = $monthStats->max('best_mood');
            $totalEntries = $monthStats->sum('total_entries');
            
            // Calculate weekly average mood
            $avgWeeklyMood = $monthStats->avg('avg_mood');
            
            // Generate mock data for new fields
            $moodFluctuation = $faker->randomFloat(2, 0.5, 3.0);
            $goodMoodDays = $faker->numberBetween(5, 20);
            $lowMoodDays = $faker->numberBetween(0, 10);
            $mostFrequentMood = $faker->numberBetween(5, 8);
            
            // Mock activity analysis
            $activities = ['work', 'exercise', 'social', 'hobbies', 'rest', 'entertainment', 'nature', 'food', 'health', 'study', 'spiritual', 'romance', 'finance', 'other'];
            $activityAnalysis = [
                'most_frequent' => [
                    'activity' => $faker->randomElement($activities),
                    'stats' => [
                        'count' => $faker->numberBetween(5, 15),
                        'avg_mood' => $faker->randomFloat(1, 5.0, 9.0),
                        'avg_productivity' => $faker->randomFloat(1, 5.0, 9.0),
                    ],
                ],
                'best_mood' => [
                    'activity' => $faker->randomElement($activities),
                    'stats' => [
                        'count' => $faker->numberBetween(3, 10),
                        'avg_mood' => $faker->randomFloat(1, 7.0, 9.5),
                        'avg_productivity' => $faker->randomFloat(1, 6.0, 9.0),
                    ],
                ],
                'worst_mood' => [
                    'activity' => $faker->randomElement($activities),
                    'stats' => [
                        'count' => $faker->numberBetween(2, 8),
                        'avg_mood' => $faker->randomFloat(1, 3.0, 6.0),
                        'avg_productivity' => $faker->randomFloat(1, 3.0, 6.0),
                    ],
                ],
            ];
            
            // Mock productivity analysis
            $productivityAnalysis = [
                'highest_day' => [
                    'date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                    'productivity' => $faker->randomFloat(1, 8.0, 10.0),
                    'mood' => $faker->randomFloat(1, 7.0, 10.0),
                    'activity' => $faker->randomElement($activities),
                ],
                'lowest_day' => [
                    'date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                    'productivity' => $faker->randomFloat(1, 1.0, 4.0),
                    'mood' => $faker->randomFloat(1, 2.0, 5.0),
                    'activity' => $faker->randomElement($activities),
                ],
                'highest_week' => $faker->randomFloat(1, 7.0, 9.0),
                'lowest_week' => $faker->randomFloat(1, 3.0, 6.0),
            ];
            
            // Mock pattern analysis
            $lowMoodPercentage = $faker->randomFloat(1, 10.0, 60.0);
            $isHighFluctuation = $faker->boolean(30);
            $isMostlyPositive = $faker->boolean(70);
            
            $patternAnalysis = [
                'low_mood_percentage' => $lowMoodPercentage,
                'is_high_fluctuation' => $isHighFluctuation,
                'is_mostly_positive' => $isMostlyPositive,
                'pattern_type' => $lowMoodPercentage > 50 ? 'chronic_stress' : 
                                ($isHighFluctuation ? 'high_fluctuation' : 
                                ($isMostlyPositive ? 'mostly_positive' : 'balanced')),
            ];
            
            MonthlyStat::create([
                'user_id' => $firstWeek->user_id,
                'month' => $monthLabel,
                'avg_mood' => $avgMood,
                'avg_weekly_mood' => $avgWeeklyMood,
                'mood_fluctuation' => $moodFluctuation,
                'good_mood_days' => $goodMoodDays,
                'low_mood_days' => $lowMoodDays,
                'most_frequent_mood' => $mostFrequentMood,
                'avg_productivity' => $avgProductivity,
                'total_entries' => $totalEntries,
                'best_mood' => $bestMood,
                'activity_analysis' => $activityAnalysis,
                'productivity_analysis' => $productivityAnalysis,
                'pattern_analysis' => $patternAnalysis,
                'feedback' => $faker->sentence(),
            ]);
        }
    }
} 