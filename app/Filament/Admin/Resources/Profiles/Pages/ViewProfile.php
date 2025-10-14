<?php

namespace App\Filament\Admin\Resources\Profiles\Pages;

use App\Filament\Admin\Resources\Profiles\ProfileResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProfile extends ViewRecord
{
    protected static string $resource = ProfileResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Profile';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage your personal information and account settings';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => route('filament.admin.resources.profiles.edit', ['record' => $this->record->getKey()]))
                ->color('primary'),
        ];
    }
}