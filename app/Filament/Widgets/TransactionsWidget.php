<?php

namespace App\Filament\Widgets;

use App\Models\FakeOrder;
use App\Models\MarketplaceOrder;
use App\Models\Order;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

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
        $month = $this->filters['month'] ?? null;
        $year = $this->filters['year'] ?? null;

        $digitalGoodsTransactions = Order::where('status', 'paid')
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->count()
            +
            FakeOrder::where('status', 'paid')
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->count();

        $physicalGoodsTransactions = MarketplaceOrder::query()
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->count();

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
