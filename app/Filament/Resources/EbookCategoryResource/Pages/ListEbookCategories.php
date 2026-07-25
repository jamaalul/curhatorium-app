<?php

namespace App\Filament\Resources\EbookCategoryResource\Pages;

use App\Filament\Resources\EbookCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEbookCategories extends ListRecords
{
    protected static string $resource = EbookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
