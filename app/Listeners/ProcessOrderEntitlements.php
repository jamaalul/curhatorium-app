<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\MembershipPlan;
use App\Models\UserEntitlement;
use App\Models\UserSubscription;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessOrderEntitlements implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        if ($order->orderable_type === MembershipPlan::class) {
            $this->processMembershipPlan($order);
        } elseif ($order->orderable_type === 'App\\Models\\CbtModule') {
            // Placeholder for CBT module
            Log::info('Placeholder for CBT module order processing', ['order_id' => $order->id]);
        } elseif ($order->orderable_type === 'App\\Models\\Ebook') {
            // Placeholder for Ebook
            Log::info('Placeholder for Ebook order processing', ['order_id' => $order->id]);
        } else {
            Log::info('No entitlement processing defined for this orderable_type', ['orderable_type' => $order->orderable_type]);
        }
    }

    /**
     * Process entitlements for a Membership Plan order.
     */
    private function processMembershipPlan($order): void
    {
        // Eager load relationships before entering the transaction
        $order->load('orderable.planBenefits');

        DB::transaction(function () use ($order) {
            $plan  = $order->orderable;
            $start = now();
            $end   = match ($plan->billing_cycle) {
                'monthly' => $start->copy()->addDays(30),
                'yearly'  => $start->copy()->addYear(),
                default   => throw new \UnexpectedValueException("Unknown billing cycle: {$plan->billing_cycle}"),
            };

            // Delete old active subscription's entitlements before replacing
            $oldSubscription = UserSubscription::where('user_id', $order->user_id)
                ->where('status', 'active')
                ->first();

            if ($oldSubscription) {
                UserEntitlement::where('user_id', $order->user_id)
                    ->where('user_subscription_id', $oldSubscription->id)
                    ->delete();
            }

            // Replace the current active subscription
            $subscription = UserSubscription::updateOrCreate(
                ['user_id' => $order->user_id, 'status' => 'active'],
                [
                    'membership_plan_id'   => $plan->id,
                    'current_period_start' => $start,
                    'current_period_end'   => $end,
                ]
            );

            $benefits = $plan->planBenefits;

            if ($benefits->isEmpty()) {
                Log::warning('No benefits found for plan', ['plan_id' => $plan->id]);
            } else {
                $now = now();

                // Bulk insert instead of one query per benefit
                UserEntitlement::insert(
                    $benefits->map(fn($benefit) => [
                        'user_id'              => $order->user_id,
                        'user_subscription_id' => $subscription->id,
                        'benefit'              => $benefit->benefit,
                        'amount_total'         => $benefit->amount,
                        'amount_used'          => 0,
                        'period_start'         => $start,
                        'period_end'           => $end,
                        'last_reset_at'        => $start,
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ])->all()
                );
            }

            Log::info('Processed membership plan', [
                'order_id'        => $order->id,
                'subscription_id' => $subscription->id,
            ]);
        });
    }
}
