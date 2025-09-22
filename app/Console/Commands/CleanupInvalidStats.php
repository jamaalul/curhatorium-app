<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeeklyStat;
use App\Models\MonthlyStat;

class CleanupInvalidStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up invalid weekly and monthly stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up invalid stats...');
        
        // Clean up weekly stats with invalid data
        $invalidWeeklyStats = WeeklyStat::where('best_mood', '<=', 0)
            ->orWhere('avg_mood', '<=', 0)
            ->orWhere('total_entries', '<=', 0);
        
        $weeklyCount = $invalidWeeklyStats->count();
        $invalidWeeklyStats->delete();
        
        // Clean up monthly stats with invalid data
        $invalidMonthlyStats = MonthlyStat::where('best_mood', '<=', 0)
            ->orWhere('avg_mood', '<=', 0)
            ->orWhere('total_entries', '<=', 0);
        
        $monthlyCount = $invalidMonthlyStats->count();
        $invalidMonthlyStats->delete();
        
        $this->info("Cleaned up {$weeklyCount} invalid weekly stats and {$monthlyCount} invalid monthly stats.");
        
        // Show remaining valid stats
        $validWeeklyCount = WeeklyStat::count();
        $validMonthlyCount = MonthlyStat::count();
        
        $this->info("Remaining valid stats: {$validWeeklyCount} weekly, {$validMonthlyCount} monthly.");
        
        return 0;
    }
} 