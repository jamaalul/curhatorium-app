<?php

namespace Tests\Feature;

use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\AiSubscriptionRequiredException;
use App\Models\AiWindow;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\UserEntitlement;
use App\Models\UserSubscription;
use App\Services\AiTokenWindowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTokenWindowServiceTest extends TestCase
{
    use RefreshDatabase;

    private AiTokenWindowService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AiTokenWindowService;
    }

    /**
     * Create a user with an active subscription and ai_window_token entitlement.
     *
     * @return array{user: User, plan: MembershipPlan, subscription: UserSubscription, entitlement: UserEntitlement}
     */
    private function createSubscribedUser(int $aiWindowHours = 24, int $tokenQuota = 1000): array
    {
        $user = User::factory()->create();

        $plan = MembershipPlan::factory()->create([
            'ai_window_hours' => $aiWindowHours,
        ]);

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDays(1),
            'current_period_end' => now()->addDays(29),
        ]);

        $entitlement = UserEntitlement::create([
            'user_id' => $user->id,
            'user_subscription_id' => $subscription->id,
            'benefit' => 'ai_window_token',
            'amount_total' => $tokenQuota,
            'amount_used' => 0,
            'period_start' => now()->subDays(1),
            'period_end' => now()->addDays(29),
        ]);

        return compact('user', 'plan', 'subscription', 'entitlement');
    }

    public function test_creates_new_window_when_none_exists(): void
    {
        ['user' => $user, 'plan' => $plan] = $this->createSubscribedUser(aiWindowHours: 48);

        $result = $this->service->resolveWindowOrFail($user);

        $this->assertInstanceOf(AiWindow::class, $result['window']);
        $this->assertInstanceOf(UserEntitlement::class, $result['entitlement']);
        $this->assertEquals(0, $result['window']->tokens_used);
        $this->assertEquals($user->id, $result['window']->user_id);

        $expectedEnd = now()->addHours(48);
        $this->assertTrue(
            $result['window']->ends_at->diffInMinutes($expectedEnd) < 1,
            'Window ends_at should be approximately 48 hours from now'
        );

        $this->assertDatabaseHas('ai_windows', [
            'user_id' => $user->id,
            'tokens_used' => 0,
        ]);
    }

    public function test_reuses_existing_active_window(): void
    {
        ['user' => $user] = $this->createSubscribedUser();

        $existingWindow = AiWindow::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addHours(23),
            'tokens_used' => 150,
        ]);

        $result = $this->service->resolveWindowOrFail($user);

        $this->assertEquals($existingWindow->id, $result['window']->id);
        $this->assertEquals(150, $result['window']->tokens_used);

        $this->assertDatabaseCount('ai_windows', 1);
    }

    public function test_rejects_when_quota_exceeded(): void
    {
        ['user' => $user] = $this->createSubscribedUser(tokenQuota: 500);

        AiWindow::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addHours(23),
            'tokens_used' => 500,
        ]);

        $this->expectException(AiQuotaExceededException::class);

        $this->service->resolveWindowOrFail($user);
    }

    public function test_allows_unlimited_entitlement(): void
    {
        ['user' => $user, 'entitlement' => $entitlement] = $this->createSubscribedUser(tokenQuota: -1);

        AiWindow::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addHours(23),
            'tokens_used' => 999999,
        ]);

        $result = $this->service->resolveWindowOrFail($user);

        $this->assertEquals(999999, $result['window']->tokens_used);
        $this->assertEquals(-1, $result['entitlement']->amount_total);
    }

    public function test_rejects_when_no_subscription(): void
    {
        $user = User::factory()->create();

        $this->expectException(AiSubscriptionRequiredException::class);

        $this->service->resolveWindowOrFail($user);
    }

    public function test_rejects_when_no_entitlement(): void
    {
        $user = User::factory()->create();

        $plan = MembershipPlan::factory()->create();

        UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDays(1),
            'current_period_end' => now()->addDays(29),
        ]);

        $this->expectException(AiSubscriptionRequiredException::class);

        $this->service->resolveWindowOrFail($user);
    }

    public function test_record_token_usage_increments_correctly(): void
    {
        ['user' => $user] = $this->createSubscribedUser();

        $window = AiWindow::factory()->create([
            'user_id' => $user->id,
            'tokens_used' => 100,
        ]);

        $this->service->recordTokenUsage($window, promptTokens: 50, completionTokens: 30);

        $this->assertDatabaseHas('ai_windows', [
            'id' => $window->id,
            'tokens_used' => 180,
        ]);
    }

    public function test_record_token_usage_skips_zero_tokens(): void
    {
        ['user' => $user] = $this->createSubscribedUser();

        $window = AiWindow::factory()->create([
            'user_id' => $user->id,
            'tokens_used' => 100,
        ]);

        $this->service->recordTokenUsage($window, promptTokens: 0, completionTokens: 0);

        $this->assertDatabaseHas('ai_windows', [
            'id' => $window->id,
            'tokens_used' => 100,
        ]);
    }

    public function test_expired_window_creates_new_one(): void
    {
        ['user' => $user, 'plan' => $plan] = $this->createSubscribedUser(aiWindowHours: 24);

        AiWindow::factory()->expired()->create([
            'user_id' => $user->id,
            'tokens_used' => 500,
        ]);

        $result = $this->service->resolveWindowOrFail($user);

        $this->assertDatabaseCount('ai_windows', 2);
        $this->assertEquals(0, $result['window']->tokens_used);

        $expectedEnd = now()->addHours(24);
        $this->assertTrue(
            $result['window']->ends_at->diffInMinutes($expectedEnd) < 1,
            'New window ends_at should be approximately 24 hours from now'
        );
    }
}
