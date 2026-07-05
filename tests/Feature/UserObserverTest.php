<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserObserverTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatingUserAutoGrantsFreePlan()
    {
        $freePlan = MembershipPlan::factory()->create([
            'id' => MembershipPlan::FREE_PLAN_ID,
            'billing_cycle' => 'monthly',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'membership_plan_id' => $freePlan->id,
            'status' => 'active',
        ]);
    }
}
