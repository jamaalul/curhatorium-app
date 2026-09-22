<?php

namespace Tests\Feature;

use App\Events\OrderPaid;
use App\Listeners\ProcessOrderEntitlements;
use App\Models\CbtModule;
use App\Models\Chapter;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Services\DokuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EClassCatalogCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_catalog_only_displays_published_modules_and_private_content_is_not_exposed(): void
    {
        $published = CbtModule::factory()->published()->create(['title' => 'Module Publik']);
        $draft = CbtModule::factory()->create(['title' => 'Module Draft']);
        $deleted = CbtModule::factory()->published()->create(['title' => 'Module Dihapus']);
        Chapter::factory()->for($published, 'module')->create([
            'title' => 'Judul Aman',
            'text_content' => 'ISI RAHASIA CHAPTER',
            'video_url' => null,
        ]);
        $deleted->delete();

        $this->get(route('e-class.index'))
            ->assertOk()
            ->assertSee('Module Publik')
            ->assertDontSee('Module Draft')
            ->assertDontSee('Module Dihapus');

        $this->get(route('e-class.show', $published))
            ->assertOk()
            ->assertSee('Judul Aman')
            ->assertDontSee('ISI RAHASIA CHAPTER');

        $this->get('/e-class/'.$draft->slug)->assertNotFound();
        $this->get('/e-class/'.$deleted->slug)->assertNotFound();
    }

    public function test_protected_routes_require_authentication_and_verified_email(): void
    {
        $module = CbtModule::factory()->published()->create();
        $unverifiedUser = User::factory()->unverified()->create();

        $this->post(route('e-class.checkout', $module))->assertRedirect(route('login'));
        $this->get(route('e-class.library'))->assertRedirect(route('login'));

        $this->actingAs($unverifiedUser)
            ->post(route('e-class.checkout', $module))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_user_can_checkout_published_module_with_doku_and_price_snapshot(): void
    {
        $this->mockDokuCharge();
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create(['price' => 125000]);

        $this->actingAs($user)
            ->post(route('e-class.checkout', $module))
            ->assertRedirect();

        $order = Order::query()->sole();

        $this->assertSame(CbtModule::class, $order->orderable_type);
        $this->assertSame($module->getKey(), $order->orderable_id);
        $this->assertSame('125000.00', $order->unit_price);
        $this->assertSame('125000.00', $order->gross_amount);
        $this->assertSame('pending', $order->status);
        $this->assertSame(1, $order->payments()->count());
    }

    public function test_checkout_reuses_a_non_expired_pending_order_without_calling_doku(): void
    {
        $this->mock(DokuService::class, function ($mock): void {
            $mock->shouldNotReceive('chargeQris');
        });
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        $order = $this->moduleOrder($user, $module, 'pending', now()->addMinutes(10));

        $this->actingAs($user)
            ->post(route('e-class.checkout', $module))
            ->assertRedirect(route('order.show', $order));

        $this->assertSame(1, Order::query()->count());
    }

    public function test_checkout_rejects_draft_owned_and_previously_paid_modules(): void
    {
        $this->mock(DokuService::class, function ($mock): void {
            $mock->shouldNotReceive('chargeQris');
        });
        $user = User::factory()->create();
        $draft = CbtModule::factory()->create();

        $this->actingAs($user)->post('/e-class/'.$draft->slug.'/checkout')->assertNotFound();

        $owned = CbtModule::factory()->published()->create();
        UserCbtModule::factory()->for($user)->for($owned, 'module')->create();
        $this->actingAs($user)->post(route('e-class.checkout', $owned))->assertConflict();

        $paid = CbtModule::factory()->published()->create();
        $this->moduleOrder($user, $paid, 'paid');
        $this->actingAs($user)->post(route('e-class.checkout', $paid))->assertConflict();
    }

    public function test_paid_module_order_creates_an_idempotent_active_entitlement(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        $firstOrder = $this->moduleOrder($user, $module, 'paid');
        $listener = app(ProcessOrderEntitlements::class);

        $listener->handle(new OrderPaid($firstOrder));
        $listener->handle(new OrderPaid($firstOrder));

        $entitlement = UserCbtModule::query()->sole();
        $this->assertSame($firstOrder->getKey(), $entitlement->source_order_id);
        $this->assertNull($entitlement->revoked_at);
        $this->assertNotNull($entitlement->granted_at);

        $entitlement->update(['revoked_at' => now()]);
        $secondOrder = $this->moduleOrder($user, $module, 'paid');
        $listener->handle(new OrderPaid($secondOrder));

        $this->assertSame(1, UserCbtModule::query()->count());
        $this->assertSame($secondOrder->getKey(), $entitlement->fresh()->source_order_id);
        $this->assertNull($entitlement->fresh()->revoked_at);
    }

    public function test_non_cbt_order_does_not_create_cbt_entitlement(): void
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();
        $order = Order::factory()->for($user)->paid()->create([
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => $plan->getKey(),
        ]);

        app(ProcessOrderEntitlements::class)->handle(new OrderPaid($order));

        $this->assertDatabaseCount('user_cbt_modules', 0);
    }

    private function mockDokuCharge(): void
    {
        $this->mock(DokuService::class, function ($mock): void {
            $mock->shouldReceive('chargeQris')->once()->andReturn([
                'transaction_id' => 'DOKU-ECLASS-1',
                'gross_amount' => '125000.00',
                'payment_type' => 'qris',
                'transaction_status' => 'pending',
                'qr_code_url' => '000201010212',
                'raw' => ['responseCode' => '2000000'],
            ]);
        });
    }

    private function moduleOrder(
        User $user,
        CbtModule $module,
        string $status,
        mixed $expiredAt = null,
    ): Order {
        return Order::factory()->for($user)->create([
            'orderable_type' => CbtModule::class,
            'orderable_id' => $module->getKey(),
            'status' => $status,
            'expired_at' => $expiredAt,
        ]);
    }
}
