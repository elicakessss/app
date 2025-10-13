<?php

namespace App\Filament\Admin\Resources\Organizations;

use App\Filament\Admin\Resources\Organizations\Pages\CreateOrganization;
use App\Filament\Admin\Resources\Organizations\Pages\EditOrganization;
use App\Filament\Admin\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Admin\Resources\Organizations\Pages\ViewOrganization;
use App\Filament\Admin\Resources\Organizations\Pages;
use App\Filament\Admin\Resources\Organizations\RelationManagers;
use App\Filament\Admin\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Admin\Resources\Organizations\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationLabel = 'Evaluations';

    protected static ?string $modelLabel = 'Organization';

    protected static ?string $pluralModelLabel = 'Organizations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        // Allow access for admin role or users with manage-organizations permission
        return auth()->user()?->hasRole('Admin') || 
               auth()->user()?->can('manage organizations') ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit($record): bool
    {
        return static::canAccess();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false; // Only admin can delete
    }

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'department']);
        
        // Filter organizations by user's department
        $user = auth()->user();
        if ($user && $user->department_id) {
            $query->where('department_id', $user->department_id);
        }
        
        return $query;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
            'evaluate-student' => Pages\EvaluateStudent::route('/{organization}/evaluate/{student}/{type}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['students']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description', 'department.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Department' => $record->department->name,
            'Year' => $record->year,
            'Students' => $record->students->count(),
        ];
    }
}
