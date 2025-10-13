<?php

namespace App\Filament\Admin\Resources\Profiles;

use App\Filament\Admin\Resources\Profiles\Pages\CreateProfile;
use App\Filament\Admin\Resources\Profiles\Pages\EditProfile;
use App\Filament\Admin\Resources\Profiles\Pages\IndexProfile;
use App\Filament\Admin\Resources\Profiles\Pages\ListProfiles;
use App\Filament\Admin\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Admin\Resources\Profiles\Schemas\ProfileInfolist;
use App\Filament\Admin\Resources\Profiles\Tables\ProfilesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfileResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $modelLabel = 'Profile';

    protected static ?string $pluralModelLabel = 'Profile';

    protected static ?int $navigationSort = 70; // Places it after Rankings (60)

    // Only allow users to edit their own profile
    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return false; // Users are created through other means
    }

    public static function canEdit($record): bool
    {
        return auth()->id() === $record->id; // Only edit own profile
    }

    public static function canDelete($record): bool
    {
        return false; // Don't allow profile deletion
    }

    public static function getEloquentQuery(): Builder
    {
        // Only show current user's profile
        return parent::getEloquentQuery()->where('id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexProfile::route('/'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }
}
