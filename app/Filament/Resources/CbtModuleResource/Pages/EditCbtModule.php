<?php

namespace App\Filament\Resources\CbtModuleResource\Pages;

use App\Filament\Resources\CbtModuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCbtModule extends EditRecord
{
    protected static string $resource = CbtModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make()
                ->visible(fn (): bool => CbtModuleResource::canForceDeleteRecord($this->getRecord())),
            Actions\RestoreAction::make(),
        ];
    }
}
