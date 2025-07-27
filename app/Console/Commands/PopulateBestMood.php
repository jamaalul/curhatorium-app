<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeeklyStat;
use App\Models\Stat;
use Carbon\Carbon;

class PopulateBestMood extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:populate-best-mood';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate best_mood values for existing weekly stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populating best_mood values for existing weekly stats...');
        
        $weeklyStats = WeeklyStat::whereNull('best_mood')->get();
        
        if ($weeklyStats->isEmpty()) {
            $this->info('No weekly stats found that need best_mood population.');
            return 0;
        }
        
        $updated = 0;
        
        foreach ($weeklyStats as $weeklyStat) {
            // Get stats for this week
            $stats = Stat::where('user_id', $weeklyStat->user_id)
                ->whereDate('created_at', '>=', $weeklyStat->week_start)
                ->whereDate('created_at', '<=', $weeklyStat->week_end)
                ->get();
            
            if ($stats->isNotEmpty()) {
                $bestMood = $stats->max('mood');
                
                if ($bestMood > 0) {
                    $weeklyStat->update(['best_mood' => $bestMood]);
                    $this->line("  - Updated weekly stat ID {$weeklyStat->id}: best_mood = {$bestMood}");
                    $updated++;
                } else {
                    $this->warn("  - Weekly stat ID {$weeklyStat->id}: No valid mood data found");
                }
            } else {
                $this->warn("  - Weekly stat ID {$weeklyStat->id}: No stats found for this week");
            }
        }
        
        $this->info("Successfully updated {$updated} weekly stats with best_mood values.");
        
        return 0;
    }
} 