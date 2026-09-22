<?php

namespace App\Filament\Widgets;

use App\Models\CbtModule;
use App\Models\Certificate;
use App\Models\UserCbtModule;
use App\Models\UserModuleProgress;
use App\ProgressStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EClassStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    /** @return array<int, Stat> */
    protected function getStats(): array
    {
        $moduleCounts = CbtModule::query()
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_count')
            ->first();
        $activeEntitlements = UserCbtModule::query()->active()->count();
        $completedUsers = UserModuleProgress::query()
            ->where('status', ProgressStatus::Completed->value)
            ->distinct()
            ->count('user_id');
        $certificates = Certificate::query()->count();

        return [
            Stat::make('Total Module', number_format((int) $moduleCounts?->total_count))
                ->icon('heroicon-m-book-open'),
            Stat::make('Module Published', number_format((int) $moduleCounts?->published_count))
                ->icon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Entitlement Aktif', number_format($activeEntitlements))
                ->icon('heroicon-m-key'),
            Stat::make('User Menyelesaikan Module', number_format($completedUsers))
                ->icon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Sertifikat', number_format($certificates))
                ->icon('heroicon-m-document-check'),
        ];
    }
}
