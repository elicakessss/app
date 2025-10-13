<?php

namespace App\Filament\Admin\Resources\Profiles\Pages;

use App\Filament\Admin\Resources\Profiles\ProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'My Profile';
    
    protected static ?string $navigationLabel = 'Profile';

    protected function getHeaderActions(): array
    {
        return [
            // No create action for profile - users edit existing profile
        ];
    }
}
