<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTicket;
use Carbon\Carbon;

class CleanupExpiredTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete tickets that have 0 limit_value or are expired from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ticket cleanup...');

        // Delete tickets with 0 limit_value (not null)
        $zeroLimitTickets = UserTicket::where('limit_value', 0)->count();
        UserTicket::where('limit_value', 0)->delete();
        $this->info("Deleted {$zeroLimitTickets} tickets with 0 limit_value.");

        // Delete expired tickets
        $expiredTickets = UserTicket::where('expires_at', '<', Carbon::now())->count();
        UserTicket::where('expires_at', '<', Carbon::now())->delete();
        $this->info("Deleted {$expiredTickets} expired tickets.");

        $totalDeleted = $zeroLimitTickets + $expiredTickets;
        $this->info("Ticket cleanup completed. Total tickets deleted: {$totalDeleted}");

        return 0;
    }
} 