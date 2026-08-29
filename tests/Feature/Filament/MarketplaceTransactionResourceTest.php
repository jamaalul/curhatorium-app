<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\MarketplaceTransactionResource;
use App\Filament\Resources\MarketplaceTransactionResource\Pages\ListMarketplaceTransactions;
use App\Models\MarketplaceOrder;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketplaceTransactionResourceTest extends TestCase
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

    public function test_marketplace_transaction_resource_page_can_render(): void
    {
        $this->get(MarketplaceTransactionResource::getUrl('index'))->assertSuccessful();
    }

    public function test_marketplace_transaction_table_displays_records_and_columns(): void
    {
        $product = Product::factory()->create([
            'name' => 'Healing Journal',
        ]);

        $order = MarketplaceOrder::create([
            'product_id' => $product->id,
            'quantity' => 3,
            'total_price' => 150000,
        ]);

        Livewire::test(ListMarketplaceTransactions::class)
            ->assertCanSeeTableRecords([$order])
            ->assertSee((string) $product->id)
            ->assertSee('Healing Journal')
            ->assertSee('3')
            ->assertSee('150.000');
    }

    public function test_marketplace_transaction_table_filters_by_product(): void
    {
        $productA = Product::factory()->create(['name' => 'Product A']);
        $productB = Product::factory()->create(['name' => 'Product B']);

        $orderA = MarketplaceOrder::create([
            'product_id' => $productA->id,
            'quantity' => 1,
            'total_price' => 50000,
        ]);

        $orderB = MarketplaceOrder::create([
            'product_id' => $productB->id,
            'quantity' => 2,
            'total_price' => 100000,
        ]);

        Livewire::test(ListMarketplaceTransactions::class)
            ->filterTable('product_id', $productA->id)
            ->assertCanSeeTableRecords([$orderA])
            ->assertCanNotSeeTableRecords([$orderB]);
    }
}
