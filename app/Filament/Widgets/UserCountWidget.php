<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserCountWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

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

        $userCount = User::query()
            ->when($month, fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year, fn ($query) => $query->whereYear('created_at', $year))
            ->count();

        return [
            Stat::make('Total Users', number_format($userCount))
                ->description('Current registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
