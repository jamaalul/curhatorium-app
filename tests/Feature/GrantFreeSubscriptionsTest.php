<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrantFreeSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function testCommandGrantsFreeToUsersWithoutSubscription()
    {
        $freePlan = MembershipPlan::factory()->create([
            'id' => MembershipPlan::FREE_PLAN_ID,
            'billing_cycle' => 'monthly',
        ]);

        $userWithoutSubscription1 = User::withoutEvents(function () { return User::factory()->create(); });
        $userWithoutSubscription2 = User::withoutEvents(function () { return User::factory()->create(); });

        $userWithSubscription = User::withoutEvents(function () { return User::factory()->create(); });
        UserSubscription::create([
            'user_id' => $userWithSubscription->id,
            'membership_plan_id' => MembershipPlan::factory()->create()->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        // Assert initial state
        $this->assertEquals(0, UserSubscription::where('user_id', $userWithoutSubscription1->id)->count());
        $this->assertEquals(0, UserSubscription::where('user_id', $userWithoutSubscription2->id)->count());

        $this->artisan('subscriptions:grant-free')
            ->expectsOutput('Finding users without an active subscription...')
            ->expectsOutput('Granted free subscriptions to 2 users.')
            ->assertExitCode(0);

        // Assert after command
        $this->assertEquals(1, UserSubscription::where('user_id', $userWithoutSubscription1->id)->where('membership_plan_id', $freePlan->id)->count());
        $this->assertEquals(1, UserSubscription::where('user_id', $userWithoutSubscription2->id)->where('membership_plan_id', $freePlan->id)->count());
    }

    public function testCommandSkipsUsersWithExistingSubscription()
    {
        $freePlan = MembershipPlan::factory()->create([
            'id' => MembershipPlan::FREE_PLAN_ID,
            'billing_cycle' => 'monthly',
        ]);

        $user = User::withoutEvents(function () {
            return User::factory()->create();
        });
        
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $freePlan->id, // They already have it
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->artisan('subscriptions:grant-free')
            ->expectsOutput('Finding users without an active subscription...')
            ->expectsOutput('Granted free subscriptions to 0 users.')
            ->assertExitCode(0);
            
        // Should still only have one
        $this->assertEquals(1, UserSubscription::where('user_id', $user->id)->count());
    }
}
