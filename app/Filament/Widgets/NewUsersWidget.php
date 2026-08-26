<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

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
        $currentPeriodCount = User::where('created_at', '>=', now()->subDays(30))->count();
        $previousPeriodCount = User::where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->count();

        $percentageChange = $previousPeriodCount > 0
            ? round((($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100)
            : ($currentPeriodCount > 0 ? 100 : 0);

        $isIncrease = $currentPeriodCount >= $previousPeriodCount;
        $formattedChange = abs($percentageChange).'%';

        return [
            Stat::make('New Users (Last 30 Days)', number_format($currentPeriodCount))
                ->description("{$formattedChange} ".($isIncrease ? 'increase' : 'decrease').' vs last period')
                ->descriptionIcon($isIncrease ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($isIncrease ? 'success' : 'danger'),
        ];
    }
}
