<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

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
        return [
            Stat::make('Total Complete Transactions', number_format((Order::where('status', 'completed')->count()) + 15))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
            Stat::make('Digital Goods Transactions', number_format(Order::where('status', 'completed')->count()))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),
            Stat::make('Physical Goods Transactions', number_format(15))
                ->description('Current complete transactions')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
        ];
    }
}
