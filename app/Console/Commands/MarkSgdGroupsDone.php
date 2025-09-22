<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SgdGroup;
use Carbon\Carbon;

class MarkSgdGroupsDone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sgd:mark-done';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark SGD groups as done 2 hours after their scheduled start time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $cutoff = $now->copy()->subHours(2);
        $groups = SgdGroup::where('is_done', false)
            ->where('schedule', '<=', $cutoff)
            ->get();

        $count = 0;
        foreach ($groups as $group) {
            $group->is_done = true;
            $group->save();
            $count++;
        }

        $this->info("Marked {$count} SGD group(s) as done.");
    }
} 