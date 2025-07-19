<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyStat;
use App\Models\Stat;
use App\Models\User;
use Faker\Factory as Faker;

class WeeklyStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;
        
        // Get all stats ordered by created_at
        $stats = Stat::orderBy('created_at', 'asc')->get();
        
        // Group stats by week (7 days each)
        $weeklyGroups = $stats->chunk(7);
        
        foreach ($weeklyGroups as $weekStats) {
            if ($weekStats->count() === 0) continue;
            
            $weekStart = $weekStats->first()->created_at->startOfWeek();
            $weekEnd = $weekStats->first()->created_at->endOfWeek();
            $avgMood = $weekStats->avg('mood');
            $avgProductivity = $weekStats->avg('productivity');
            $bestMood = $weekStats->max('mood');
            $totalEntries = $weekStats->count();
            
            WeeklyStat::create([
                'user_id' => $weekStats->first()->user_id,
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'avg_mood' => $avgMood,
                'avg_productivity' => $avgProductivity,
                'total_entries' => $totalEntries,
                'best_mood' => $bestMood,
                'feedback' => $faker->sentence(),
            ]);
        }
    }
} 