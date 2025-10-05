<?php

namespace App\Filament\Resources\Ranks\Pages;

use App\Filament\Resources\Ranks\RankResource;
use Filament\Resources\Pages\ViewRecord;

class ViewRank extends ViewRecord
{
    protected static string $resource = RankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed - read-only resource
        ];
    }
}
