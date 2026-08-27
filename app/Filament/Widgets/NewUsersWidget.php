<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\FakeUser;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewUsersWidget extends BaseWidget
{
    use InteractsWithPageFilters;

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
        $month = $this->filters['month'] ?? null;
        $year = $this->filters['year'] ?? null;

        if ($month && $year) {
            $currentDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
            $previousDate = $currentDate->copy()->subMonth();

            $currentPeriodCount = (FakeUser::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count()) +_(User::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count());

            $previousPeriodCount = (FakeUser::whereMonth('created_at', $previousDate->month)
                ->whereYear('created_at', $previousDate->year)
                ->count()) +_(User::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count());

            $periodLabel = 'vs last month';
        } elseif ($year) {
            $currentPeriodCount = (FakeUser::whereYear('created_at', (int) $year)
                ->count()) +_(User::whereYear('created_at', (int) $year)->count());
            $previousPeriodCount = (FakeUser::whereYear('created_at', (int) $year - 1)
                ->count()) +_(User::whereYear('created_at', (int) $year - 1)->count());
            $periodLabel = 'vs last year growth';
        } elseif ($month) {
            $currentYear = now()->year;
            $currentDate = Carbon::createFromDate($currentYear, (int) $month, 1)->startOfMonth();
            $previousDate = $currentDate->copy()->subMonth();

            $currentPeriodCount = (User::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count()) + (FakeUser::whereMonth('created_at', $currentDate->month)
                ->whereYear('created_at', $currentDate->year)
                ->count());

            $previousPeriodCount = (User::whereMonth('created_at', $previousDate->month)
                ->whereYear('created_at', $previousDate->year)
                ->count()) + (FakeUser::whereMonth('created_at', $previousDate->month)
                ->whereYear('created_at', $previousDate->year)
                ->count());

            $periodLabel = 'vs last month';
        } else {
            $currentPeriodCount = (User::where('created_at', '>=', now()->subDays(30))->count()) + (FakeUser::where('created_at', '>=', now()->subDays(30))->count());
            $previousPeriodCount = (User::where('created_at', '>=', now()->subDays(60))
                ->where('created_at', '<', now()->subDays(30))->count()) + (FakeUser::where('created_at', '>=', now()->subDays(60))->where('created_at', '<', now()->subDays(30))->count());

            $periodLabel = 'vs last period';
        }

        $percentageChange = $previousPeriodCount > 0
            ? round((($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100)
            : ($currentPeriodCount > 0 ? 100 : 0);

        $isIncrease = $currentPeriodCount >= $previousPeriodCount;
        $formattedChange = abs($percentageChange).'%';

        return [
            Stat::make('New Users', number_format($currentPeriodCount))
                ->description("{$formattedChange} ".($isIncrease ? 'increase' : 'decrease')." {$periodLabel}")
                ->descriptionIcon($isIncrease ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($isIncrease ? 'success' : 'danger'),
        ];
    }
}
