<?php

namespace App\Console\Commands;

use App\Models\MembershipPlan;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RevertExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:revert-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revert expired subscriptions to the free membership plan';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $expiredSubscriptions = UserSubscription::with('user')
            ->where('status', 'active')
            // ->where('membership_plan_id', '!=', MembershipPlan::FREE_PLAN_ID)
            ->where('current_period_end', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');

            return;
        }

        $count = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscriptionService->grantFreePlan($subscription->user);
            $count++;
        }

        $this->info("Reverted {$count} expired subscriptions to Free plan.");
        Log::info('Reverted expired subscriptions to Free plan', ['count' => $count]);
    }
}
