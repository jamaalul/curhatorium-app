<?php

namespace App\Console\Commands;

use App\Models\MembershipPlan;
use App\Models\UserEntitlement;
use App\Models\UserSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
    public function handle()
    {
        $expiredSubscriptions = UserSubscription::query()
            ->where('status', 'active')
            ->where('membership_plan_id', '!=', 1)
            ->where('current_period_end', '<', now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');

            return;
        }

        $freePlan = MembershipPlan::with('planBenefits')->find(1);

        if (! $freePlan) {
            $this->error('Free membership plan (ID: 1) not found.');

            return;
        }

        $count = 0;

        foreach ($expiredSubscriptions as $subscription) {
            DB::transaction(function () use ($subscription, $freePlan, &$count) {
                $start = now();
                $end = match ($freePlan->billing_cycle) {
                    'monthly' => $start->copy()->addMonth(),
                    'yearly' => $start->copy()->addYear(),
                    default => $start->copy()->addMonth(),
                };

                $subscription->update([
                    'membership_plan_id' => $freePlan->id,
                    'current_period_start' => $start,
                    'current_period_end' => $end,
                ]);

                // Replace entitlements
                UserEntitlement::where('user_id', $subscription->user_id)
                    ->where('user_subscription_id', $subscription->id)
                    ->delete();

                foreach ($freePlan->planBenefits as $benefit) {
                    UserEntitlement::create([
                        'user_id' => $subscription->user_id,
                        'user_subscription_id' => $subscription->id,
                        'benefit' => $benefit->benefit,
                        'amount_total' => $benefit->amount,
                        'amount_used' => 0,
                        'period_start' => $start,
                        'period_end' => $end,
                        'last_reset_at' => $start,
                    ]);
                }

                $count++;
            });
        }

        $this->info("Reverted {$count} expired subscriptions to Free plan.");
        Log::info('Reverted expired subscriptions to Free plan', ['count' => $count]);
    }
}
