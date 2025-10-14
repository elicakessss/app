<?php

namespace App\Filament\Student\Resources\Profiles\Pages;

use App\Filament\Student\Resources\Profiles\ProfileResource;
use Filament\Resources\Pages\ListRecords;

class IndexProfile extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    public function mount(): void
    {
        // Redirect to the current student's profile view
        $student = auth('student')->user();
        
        if ($student) {
            $this->redirect(ProfileResource::getUrl('view', ['record' => $student->id]));
        }
    }
}