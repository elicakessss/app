<?php

namespace App\Filament\Student\Resources\Profiles\Pages;

use App\Filament\Student\Resources\Profiles\ProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Profile';

    public function getTitle(): string | Htmlable
    {
        return 'Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil'),
        ];
    }
}