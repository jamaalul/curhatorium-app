<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    public function getTableRecordKey(Model $record): string
    {
        return (string) ($record->order_ref ?? $record->getKey());
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
