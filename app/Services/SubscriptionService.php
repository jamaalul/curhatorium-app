<?php

namespace App\Services;

use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\UserEntitlement;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Grant the free membership plan to a user.
     *
     * Creates an active UserSubscription and provisions the
     * corresponding UserEntitlement rows from the plan's benefits.
     */
    public function grantFreePlan(User $user): ?UserSubscription
    {
        $freePlan = MembershipPlan::with('planBenefits')->find(MembershipPlan::FREE_PLAN_ID);

        if (! $freePlan) {
            Log::error('Free membership plan not found', ['plan_id' => MembershipPlan::FREE_PLAN_ID]);

            return null;
        }

        return DB::transaction(function () use ($user, $freePlan) {
            $start = now();
            $end = match ($freePlan->billing_cycle) {
                'monthly' => $start->copy()->addDays(30),
                'yearly' => $start->copy()->addYear(),
                default => $start->copy()->addDays(30),
            };

            // Delete old entitlements if replacing an existing active subscription
            $oldSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($oldSubscription) {
                UserEntitlement::where('user_id', $user->id)
                    ->where('user_subscription_id', $oldSubscription->id)
                    ->delete();
            }

            // Create or replace the active subscription
            $subscription = UserSubscription::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                [
                    'membership_plan_id' => $freePlan->id,
                    'current_period_start' => $start,
                    'current_period_end' => $end,
                ]
            );

            // Provision entitlements
            $benefits = $freePlan->planBenefits;

            if ($benefits->isNotEmpty()) {
                $now = now();

                UserEntitlement::insert(
                    $benefits->map(fn ($benefit) => [
                        'user_id' => $user->id,
                        'user_subscription_id' => $subscription->id,
                        'benefit' => $benefit->benefit,
                        'amount_total' => $benefit->amount,
                        'amount_used' => 0,
                        'period_start' => $start,
                        'period_end' => $end,
                        'last_reset_at' => $start,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all()
                );
            }

            Log::info('Granted free plan to user', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription;
        });
    }
}
