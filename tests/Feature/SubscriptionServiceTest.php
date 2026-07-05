<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\PlanBenefit;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserEntitlement;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriptionService = new SubscriptionService();
    }

    public function testGrantFreePlanCreatesSubscriptionAndEntitlements()
    {
        $freePlan = MembershipPlan::factory()->create([
            'id' => MembershipPlan::FREE_PLAN_ID,
            'name' => 'Free',
            'billing_cycle' => 'monthly',
        ]);

        PlanBenefit::create([
            'membership_plan_id' => $freePlan->id,
            'benefit' => 'snt_rgr_chat',
            'amount' => 0,
        ]);
        
        PlanBenefit::create([
            'membership_plan_id' => $freePlan->id,
            'benefit' => 'ai_window_token',
            'amount' => 25000,
        ]);

        $user = User::withoutEvents(function () { return User::factory()->create(); });

        $subscription = $this->subscriptionService->grantFreePlan($user);

        $this->assertNotNull($subscription);
        $this->assertEquals($freePlan->id, $subscription->membership_plan_id);
        $this->assertEquals('active', $subscription->status);
        
        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'membership_plan_id' => $freePlan->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('user_entitlements', [
            'user_id' => $user->id,
            'benefit' => 'snt_rgr_chat',
            'amount_total' => 0,
            'amount_used' => 0,
        ]);
        
        $this->assertDatabaseHas('user_entitlements', [
            'user_id' => $user->id,
            'benefit' => 'ai_window_token',
            'amount_total' => 25000,
            'amount_used' => 0,
        ]);
    }

    public function testGrantFreePlanReplacesExistingActiveSubscription()
    {
        $freePlan = MembershipPlan::factory()->create([
            'id' => MembershipPlan::FREE_PLAN_ID,
            'billing_cycle' => 'monthly',
        ]);

        $user = User::withoutEvents(function () { return User::factory()->create(); });

        $oldPlan = MembershipPlan::factory()->create();
        
        $oldSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $oldPlan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);
        
        UserEntitlement::create([
            'user_id' => $user->id,
            'user_subscription_id' => $oldSubscription->id,
            'benefit' => 'snt_rgr_chat',
            'amount_total' => 10,
            'amount_used' => 0,
            'period_start' => now(),
            'period_end' => now()->addMonth(),
            'last_reset_at' => now(),
        ]);

        $newSubscription = $this->subscriptionService->grantFreePlan($user);

        $this->assertEquals($oldSubscription->id, $newSubscription->id);
        
        $this->assertDatabaseMissing('user_entitlements', [
            'user_id' => $user->id,
            'user_subscription_id' => $oldSubscription->id,
            'benefit' => 'snt_rgr_chat',
        ]);
    }

    public function testGrantFreePlanSkipsWhenFreePlanNotFound()
    {
        Log::shouldReceive('error')->once();

        $user = User::withoutEvents(function () { return User::factory()->create(); });

        $subscription = $this->subscriptionService->grantFreePlan($user);

        $this->assertNull($subscription);
        $this->assertDatabaseEmpty('user_subscriptions');
    }
}
