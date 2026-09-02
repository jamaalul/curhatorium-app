<?php

namespace App\Services;

use App\Models\AiWindow;
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
     */
    public function grantFreePlan(User $user): ?UserSubscription
    {
        return $this->grantPlan($user, MembershipPlan::FREE_PLAN_ID);
    }

    /**
     * Grant a specific membership plan to a user.
     */
    public function grantPlan(User $user, int $planId): ?UserSubscription
    {
        $plan = MembershipPlan::with('planBenefits')->find($planId);

        if (! $plan) {
            Log::error('Membership plan not found', ['plan_id' => $planId]);

            return null;
        }

        return DB::transaction(function () use ($user, $plan) {
            $start = now();
            $end = match ($plan->billing_cycle) {
                'monthly' => $start->copy()->addDays(30),
                'yearly' => $start->copy()->addYear(),
                default => $start->copy()->addDays(30),
            };

            // Delete old entitlements if replacing an existing active subscription
            $oldSubscription = UserSubscription::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($oldSubscription) {
                UserEntitlement::query()
                    ->where('user_id', $user->id)
                    ->where('user_subscription_id', $oldSubscription->id)
                    ->delete();
            }

            // Create or replace the active subscription
            $subscription = UserSubscription::query()->updateOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                [
                    'membership_plan_id' => $plan->id,
                    'current_period_start' => $start,
                    'current_period_end' => $end,
                ]
            );

            // Provision entitlements
            $benefits = $plan->planBenefits;

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

            // Close the current active AI window so a new one is created on next message
            $activeWindow = AiWindow::activeForUser($user->id)->first();
            if ($activeWindow) {
                $activeWindow->update(['ends_at' => now()]);
            }

            Log::info('Granted membership plan to user', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'subscription_id' => $subscription->id,
            ]);

            return $subscription;
        });
    }
}
