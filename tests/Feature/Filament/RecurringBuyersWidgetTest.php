<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\RecurringBuyersWidget;
use App\Models\FakeOrder;
use App\Models\Order;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecurringBuyersWidgetTest extends TestCase
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

    public function test_recurring_buyers_widget_can_render(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // user1 has 2 paid orders in orders table => recurring
        Order::factory()->paid()->count(2)->create([
            'user_id' => $user1->id,
        ]);

        // user2 has 1 paid order in orders and 1 paid in fake_orders => recurring
        Order::factory()->paid()->create([
            'user_id' => $user2->id,
        ]);
        FakeOrder::create([
            'order_ref' => 'FAKE-001',
            'user_id' => $user2->id,
            'orderable_type' => 'App\Models\Ebook',
            'orderable_id' => 1,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
        ]);

        // user3 only has 1 order => not recurring
        Order::factory()->paid()->create([
            'user_id' => $user3->id,
        ]);

        Livewire::test(RecurringBuyersWidget::class)
            ->assertSuccessful()
            ->assertSee('Recurring Buyers')
            ->assertSee('2');
    }

    public function test_recurring_buyers_widget_filters_by_month_and_year(): void
    {
        $user1 = User::factory()->create();

        // 2 orders in May 2025
        Order::factory()->paid()->create([
            'user_id' => $user1->id,
            'created_at' => '2025-05-10 10:00:00',
        ]);
        FakeOrder::create([
            'order_ref' => 'FAKE-002',
            'user_id' => $user1->id,
            'orderable_type' => 'App\Models\Ebook',
            'orderable_id' => 1,
            'quantity' => 1,
            'unit_price' => 50000,
            'gross_amount' => 50000,
            'status' => 'paid',
            'created_at' => '2025-05-15 10:00:00',
        ]);

        // 1 order in June 2025
        Order::factory()->paid()->create([
            'user_id' => $user1->id,
            'created_at' => '2025-06-10 10:00:00',
        ]);

        Livewire::test(RecurringBuyersWidget::class, ['filters' => ['month' => '5', 'year' => '2025']])
            ->assertSuccessful()
            ->assertSee('Recurring Buyers')
            ->assertSee('1');

        Livewire::test(RecurringBuyersWidget::class, ['filters' => ['month' => '6', 'year' => '2025']])
            ->assertSuccessful()
            ->assertSee('Recurring Buyers')
            ->assertSee('0');
    }

    public function test_recurring_buyers_widget_column_span_configuration(): void
    {
        $widget = new RecurringBuyersWidget;

        $this->assertEquals([
            'default' => 'full',
            'md' => 1,
            'lg' => 1,
        ], $widget->getColumnSpan());
    }
}
