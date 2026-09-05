<?php

namespace App\Filament\Resources\CbtModuleResource\Pages;

use App\Filament\Resources\CbtModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCbtModules extends ListRecords
{
    protected static string $resource = CbtModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
