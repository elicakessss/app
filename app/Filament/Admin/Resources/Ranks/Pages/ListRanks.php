<?php

namespace App\Filament\Admin\Resources\Ranks\Pages;

use App\Filament\Admin\Resources\Ranks\RankResource;
use Filament\Resources\Pages\ListRecords;

class ListRanks extends ListRecords
{
    protected static string $resource = RankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed - read-only resource
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Review student ranks based on evaluation scores.';
    }
}
