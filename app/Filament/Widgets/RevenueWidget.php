<?php

namespace App\Filament\Widgets;

use App\Models\FakeOrder;
use App\Models\MarketplaceOrder;
use App\Models\Order;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

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
            ->sum('gross_amount')
            +
            FakeOrder::where('status', 'paid')
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->sum('gross_amount');

        $physicalGoodsTransactions = MarketplaceOrder::query()
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->sum('total_price');

        $totalCompleteTransactions = $digitalGoodsTransactions + $physicalGoodsTransactions;

        return [
            Stat::make('Total Revenue', 'Rp ' . number_format($totalCompleteTransactions))
                ->description('This month completed transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Digital Goods Revenue', 'Rp ' . number_format($digitalGoodsTransactions))
                ->description('This month completed transactions')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),
            Stat::make('Physical Goods Revenue', 'Rp ' . number_format($physicalGoodsTransactions))
                ->description('This month completed transactions')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
        ];
    }
}
