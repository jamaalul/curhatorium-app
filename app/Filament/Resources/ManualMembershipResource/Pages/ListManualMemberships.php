<?php

namespace App\Filament\Resources\ManualMembershipResource\Pages;

use App\Filament\Resources\ManualMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManualMemberships extends ListRecords
{
    protected static string $resource = ManualMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Grant New Membership'),
        ];
    }
} 