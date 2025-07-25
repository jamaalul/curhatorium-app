<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('weekly:stat-summary')->sundays()->at('00:10');
        $schedule->command('monthly:stat-summary')->monthlyOn(1, '00:20');
        $schedule->command('membership:grant-calm-starter')->monthlyOn(1, '00:05');
        $schedule->command('sessions:cleanup-expired')->everyMinute();
        $schedule->command('tickets:cleanup-expired')->daily()->at('02:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
} 