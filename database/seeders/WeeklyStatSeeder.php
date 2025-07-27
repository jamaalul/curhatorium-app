<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeeklyStat;
use App\Models\Stat;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class WeeklyStatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userIds = User::pluck('id')->all();
        if (empty($userIds)) return;
        
        // Get all stats ordered by created_at
        $stats = Stat::orderBy('created_at', 'asc')->get();
        
        // Group stats by user and week
        $userWeekGroups = $stats->groupBy(function($stat) {
            return $stat->user_id . '_' . $stat->created_at->format('Y-W');
        });
        
        foreach ($userWeekGroups as $userWeekKey => $weekStats) {
            if ($weekStats->count() === 0) continue;
            
            $weekStart = $weekStats->first()->created_at->startOfWeek();
            $weekEnd = $weekStats->first()->created_at->endOfWeek();
            $avgMood = $weekStats->avg('mood');
            $avgProductivity = $weekStats->avg('productivity');
            $bestMood = $weekStats->max('mood');
            $totalEntries = $weekStats->count();
            
            // Ensure we have valid data
            if ($avgMood > 0 && $bestMood > 0) {
                WeeklyStat::create([
                    'user_id' => $weekStats->first()->user_id,
                    'week_start' => $weekStart->format('Y-m-d'),
                    'week_end' => $weekEnd->format('Y-m-d'),
                    'avg_mood' => round($avgMood, 2),
                    'avg_productivity' => round($avgProductivity, 2),
                    'total_entries' => $totalEntries,
                    'best_mood' => $bestMood,
                    'feedback' => $faker->sentence(),
                ]);
            }
        }
    }
} 