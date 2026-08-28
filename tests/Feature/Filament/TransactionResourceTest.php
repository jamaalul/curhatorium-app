<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Models\Ebook;
use App\Models\FakeOrder;
use App\Models\MembershipPlan;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionResourceTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs(User::factory()->create([
            'is_admin' => true,
        ]));
    }

    public function test_transaction_resource_page_can_render(): void
    {
        $this->get(TransactionResource::getUrl('index'))->assertSuccessful();
    }

    public function test_transaction_table_displays_both_orders_and_fake_orders(): void
    {
        $user = User::factory()->create();

        $realOrder = Order::create([
            'order_ref' => 'ORD-REAL-001',
            'user_id' => $user->id,
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => 10,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        $fakeOrder = FakeOrder::create([
            'order_ref' => 'ORD-FAKE-002',
            'user_id' => $user->id,
            'orderable_type' => Ebook::class,
            'orderable_id' => 20,
            'quantity' => 1,
            'unit_price' => 75000,
            'gross_amount' => 75000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->assertCanSeeTableRecords([$realOrder, $fakeOrder])
            ->assertSee('ORD-REAL-001')
            ->assertSee('ORD-FAKE-002');
    }

    public function test_transaction_table_filters_by_status(): void
    {
        $user = User::factory()->create();

        $paidOrder = Order::create([
            'order_ref' => 'ORD-PAID-001',
            'user_id' => $user->id,
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => 10,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        $pendingFakeOrder = FakeOrder::create([
            'order_ref' => 'ORD-PENDING-002',
            'user_id' => $user->id,
            'orderable_type' => Ebook::class,
            'orderable_id' => 20,
            'quantity' => 1,
            'unit_price' => 75000,
            'gross_amount' => 75000,
            'status' => 'pending',
        ]);

        Livewire::test(ListTransactions::class)
            ->filterTable('status', 'paid')
            ->assertCanSeeTableRecords([$paidOrder])
            ->assertCanNotSeeTableRecords([$pendingFakeOrder]);

        Livewire::test(ListTransactions::class)
            ->filterTable('status', 'pending')
            ->assertCanSeeTableRecords([$pendingFakeOrder])
            ->assertCanNotSeeTableRecords([$paidOrder]);
    }

    public function test_transaction_table_filters_by_orderable_type(): void
    {
        $user = User::factory()->create();

        $planOrder = Order::create([
            'order_ref' => 'ORD-PLAN-001',
            'user_id' => $user->id,
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => 10,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        $ebookFakeOrder = FakeOrder::create([
            'order_ref' => 'ORD-EBOOK-002',
            'user_id' => $user->id,
            'orderable_type' => Ebook::class,
            'orderable_id' => 20,
            'quantity' => 1,
            'unit_price' => 75000,
            'gross_amount' => 75000,
            'status' => 'paid',
        ]);

        Livewire::test(ListTransactions::class)
            ->filterTable('orderable_type', MembershipPlan::class)
            ->assertCanSeeTableRecords([$planOrder])
            ->assertCanNotSeeTableRecords([$ebookFakeOrder]);

        Livewire::test(ListTransactions::class)
            ->filterTable('orderable_type', Ebook::class)
            ->assertCanSeeTableRecords([$ebookFakeOrder])
            ->assertCanNotSeeTableRecords([$planOrder]);
    }

    public function test_transaction_table_filters_by_orderable_id(): void
    {
        $user = User::factory()->create();

        $order1 = Order::create([
            'order_ref' => 'ORD-ID-10',
            'user_id' => $user->id,
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => 10,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        $order2 = FakeOrder::create([
            'order_ref' => 'ORD-ID-20',
            'user_id' => $user->id,
            'orderable_type' => MembershipPlan::class,
            'orderable_id' => 20,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        Livewire::test(ListTransactions::class)
            ->filterTable('orderable_id', [
                'orderable_id' => 10,
            ])
            ->assertCanSeeTableRecords([$order1])
            ->assertCanNotSeeTableRecords([$order2]);
    }
}
