<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\TransactionsWidget;
use App\Models\FakeOrder;
use App\Models\MarketplaceOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionsWidgetTest extends TestCase
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

    public function test_transactions_widget_can_render(): void
    {
        Order::factory()->paid()->count(2)->create();
        FakeOrder::create([
            'order_ref' => 'FAKE-123',
            'status' => 'paid',
        ]);
        $product = Product::factory()->create();
        MarketplaceOrder::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'total_price' => 50000,
        ]);

        Livewire::test(TransactionsWidget::class)
            ->assertSuccessful()
            ->assertSee('Total Complete Transactions')
            ->assertSee('Digital Goods Transactions')
            ->assertSee('Physical Goods Transactions');
    }

    public function test_transactions_widget_filters_by_month_and_year(): void
    {
        // Paid order in May 2025
        Order::factory()->paid()->create([
            'created_at' => '2025-05-10 10:00:00',
        ]);

        // Paid order in June 2025
        Order::factory()->paid()->create([
            'created_at' => '2025-06-10 10:00:00',
        ]);

        $product = Product::factory()->create();

        // Marketplace order in May 2025
        MarketplaceOrder::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'total_price' => 50000,
            'created_at' => '2025-05-15 10:00:00',
        ]);

        // Marketplace order in June 2025
        MarketplaceOrder::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'total_price' => 50000,
            'created_at' => '2025-06-15 10:00:00',
        ]);

        Livewire::test(TransactionsWidget::class, ['filters' => ['month' => '5', 'year' => '2025']])
            ->assertSuccessful()
            ->assertSee('Total Complete Transactions')
            ->assertSee('Digital Goods Transactions')
            ->assertSee('Physical Goods Transactions');
    }

    public function test_transactions_widget_column_span_configuration(): void
    {
        $widget = new TransactionsWidget;

        $this->assertEquals([
            'default' => 'full',
            'md' => 'full',
            'lg' => 'full',
        ], $widget->getColumnSpan());
    }
}
