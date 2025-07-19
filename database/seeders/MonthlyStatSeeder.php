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
            
            MonthlyStat::create([
                'user_id' => $firstWeek->user_id,
                'month' => $monthLabel,
                'avg_mood' => $avgMood,
                'avg_productivity' => $avgProductivity,
                'total_entries' => $totalEntries,
                'best_mood' => $bestMood,
                'feedback' => $faker->sentence(),
            ]);
        }
    }
} 