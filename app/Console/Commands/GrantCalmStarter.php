<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
use App\Models\UserMembership;
use App\Models\MembershipTicket;
use App\Models\UserTicket;
use Carbon\Carbon;

class GrantCalmStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:grant-calm-starter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant Calm Starter membership and tickets to all users who do not have an active Calm Starter for the current month.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $calmStarter = Membership::where('name', 'Calm Starter')->first();
        if (!$calmStarter) {
            $this->error('Calm Starter membership not found.');
            return 1;
        }

        $users = User::all();
        $granted = 0;
        foreach ($users as $user) {
            $hasActive = UserMembership::where('user_id', $user->id)
                ->where('membership_id', $calmStarter->id)
                ->where('started_at', '>=', $monthStart)
                ->where('expires_at', '<=', $monthEnd)
                ->exists();
            if ($hasActive) {
                continue;
            }

            $expires = $monthEnd->copy()->endOfDay();
            $userMembership = UserMembership::create([
                'user_id' => $user->id,
                'membership_id' => $calmStarter->id,
                'started_at' => $now,
                'expires_at' => $expires,
            ]);

            $tickets = MembershipTicket::where('membership_id', $calmStarter->id)->get();
            foreach ($tickets as $ticket) {
                $isUnlimited = $ticket->limit_type === 'unlimited';
                $value = $isUnlimited ? null : $ticket->limit_value;
                UserTicket::create([
                    'user_id' => $user->id,
                    'ticket_type' => $ticket->ticket_type,
                    'limit_type' => $ticket->limit_type,
                    'limit_value' => $isUnlimited ? null : $value,
                    'remaining_value' => $isUnlimited ? null : $value,
                    'expires_at' => $expires,
                ]);
            }
            $granted++;
        }
        $this->info("Calm Starter granted to $granted users.");
        return 0;
    }
} 