<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EClassStats;
use Filament\Pages\Page;

class EClassOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'E-Class';

    protected static ?string $navigationLabel = 'Ringkasan E-Class';

    protected static ?string $title = 'Ringkasan E-Class';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.e-class-overview';

    /** @return array<class-string> */
    protected function getHeaderWidgets(): array
    {
        return [
            EClassStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return [
            'md' => 2,
            'xl' => 5,
        ];
    }
}
