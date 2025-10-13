<?php

namespace App\Filament\Admin\Resources\Profiles\Pages;

use App\Filament\Admin\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\Page;

class IndexProfile extends Page
{
    protected static string $resource = ProfileResource::class;

    public function mount(): void
    {
        // Redirect to the current user's profile view
        $this->redirect(route('filament.admin.resources.profiles.view', ['record' => auth()->id()]));
    }
}