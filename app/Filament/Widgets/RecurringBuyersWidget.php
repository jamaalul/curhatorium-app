<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RecurringBuyersWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    /**
     * @var int | string | array<string, int | string | null>
     */
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
        'lg' => 1,
    ];

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $month = $this->filters['month'] ?? null;
        $year = $this->filters['year'] ?? null;

        // 1. Get IDs of users who are lifetime recurring buyers (> 1 order ever)
        $allTimeOrders = DB::table('orders')->select('user_id')->where('status', 'paid')->whereNotNull('user_id')
            ->unionAll(DB::table('fake_orders')->select('user_id')->where('status', 'paid')->whereNotNull('user_id'));

        $recurringUserIds = DB::query()
            ->fromSub($allTimeOrders, 'all_time')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        // 2. Check how many of those recurring buyers made a purchase in the filtered month/year
        $filteredOrders = DB::table('orders')->select('user_id')->where('status', 'paid')->whereNotNull('user_id')
            ->unionAll(DB::table('fake_orders')->select('user_id')->where('status', 'paid')->whereNotNull('user_id'))
            ->when($month, fn ($q) => $q->whereMonth('created_at', $month))
            ->when($year, fn ($q) => $q->whereYear('created_at', $year));

        $recurringBuyersCount = DB::query()
            ->fromSub($filteredOrders, 'filtered')
            ->whereIn('user_id', $recurringUserIds)
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('Recurring Buyers', number_format($recurringBuyersCount))
                ->description('Recurring buyers active in this period')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),
        ];
    }
}
