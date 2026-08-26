<?php

namespace App\Filament\Widgets;

use App\Models\FakeOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\MarketplaceOrder;

class TransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    /**
     * @var int | string | array<string, int | string | null>
     */
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 'full',
        'lg' => 'full',
    ];

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $digitalGoodsTransactions = (Order::where('status', 'completed')->count()) + (FakeOrder::where('status', 'completed')->count());
        $physicalGoodsTransactions = MarketplaceOrder::count();
        $totalCompleteTransactions = $digitalGoodsTransactions + $physicalGoodsTransactions;

        return [
            Stat::make('Total Complete Transactions', number_format($totalCompleteTransactions))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Digital Goods Transactions', number_format($digitalGoodsTransactions))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),
            Stat::make('Physical Goods Transactions', number_format($physicalGoodsTransactions))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
        ];
    }
}
