<?php

namespace App\Filament\Resources\ShareTalkPaymentResource\Pages;

use App\Filament\Resources\ShareTalkPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShareTalkPayments extends ListRecords
{
    protected static string $resource = ShareTalkPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since this is read-only
        ];
    }
} 