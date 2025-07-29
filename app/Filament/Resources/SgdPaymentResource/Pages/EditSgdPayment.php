<?php

namespace App\Filament\Resources\SgdPaymentResource\Pages;

use App\Filament\Resources\SgdPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSgdPayment extends EditRecord
{
    protected static string $resource = SgdPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
