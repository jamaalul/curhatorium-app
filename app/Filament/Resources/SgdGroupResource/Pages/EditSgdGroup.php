<?php

namespace App\Filament\Resources\SgdGroupResource\Pages;

use App\Filament\Resources\SgdGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSgdGroup extends EditRecord
{
    protected static string $resource = SgdGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
