<?php

namespace App\Filament\Student\Resources\Profiles\Pages;

use App\Filament\Student\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditProfile extends EditRecord
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Edit Profile';

    protected function getHeaderActions(): array
    {
        return [
            // No delete action for profile - students shouldn't delete their accounts
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}