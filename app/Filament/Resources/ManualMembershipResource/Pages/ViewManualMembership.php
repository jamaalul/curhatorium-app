<?php

namespace App\Filament\Resources\ManualMembershipResource\Pages;

use App\Filament\Resources\ManualMembershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManualMembership extends ViewRecord
{
    protected static string $resource = ManualMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed for viewing granted memberships
        ];
    }
} 