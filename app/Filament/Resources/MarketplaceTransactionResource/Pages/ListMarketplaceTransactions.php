<?php

namespace App\Filament\Resources\MarketplaceTransactionResource\Pages;

use App\Filament\Resources\MarketplaceTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListMarketplaceTransactions extends ListRecords
{
    protected static string $resource = MarketplaceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
