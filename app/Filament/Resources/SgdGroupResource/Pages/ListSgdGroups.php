<?php

namespace App\Filament\Resources\SgdGroupResource\Pages;

use App\Filament\Resources\SgdGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSgdGroups extends ListRecords
{
    protected static string $resource = SgdGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
