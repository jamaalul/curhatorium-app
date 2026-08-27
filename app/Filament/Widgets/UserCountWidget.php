<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\FakeUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserCountWidget extends BaseWidget
{

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

        $userCount = (User::count()) + (FakeUser::count());

        return [
            Stat::make('Total Users', number_format($userCount))
                ->description('All time registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
