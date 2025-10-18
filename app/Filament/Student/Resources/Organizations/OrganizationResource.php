<?php

namespace App\Filament\Student\Resources\Organizations;

use App\Filament\Student\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Student\Resources\Organizations\Pages\SelfEvaluate;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
// ...existing imports...
use Illuminate\Database\Eloquent\Builder;

/**
 * Student Organizations Resource
 * 
 * Displays organizations the student belongs to with self-evaluation capabilities
 */
class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'My Evaluations';

    protected static ?string $modelLabel = 'Evaluation Task';

    protected static ?string $pluralModelLabel = 'My Evaluations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth('student')->check();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return false; // Students can't create organizations
    }

    public static function canEdit($record): bool
    {
        return false; // Students can't edit organizations
    }

    public static function canDelete($record): bool
    {
        return false; // Students can't delete organizations
    }

    public static function canView($record): bool
    {
        return false; // No individual view needed
    }

    // Table is handled by ListOrganizations page

    public static function getEloquentQuery(): Builder
    {
        // Only show organizations the current student belongs to
        return parent::getEloquentQuery()
            ->whereHas('students', function (Builder $query) {
                $query->where('student_id', auth('student')->id());
            })
            ->with(['department', 'students']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'self-evaluate' => SelfEvaluate::route('/{organization}/self-evaluate'),
            'peer-evaluate' => \App\Filament\Student\Resources\Organizations\Pages\PeerEvaluate::route('/{organization}/{student}/peer-evaluate'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}