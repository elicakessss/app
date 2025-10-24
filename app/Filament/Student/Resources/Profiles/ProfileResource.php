<?php

namespace App\Filament\Student\Resources\Profiles;

use App\Filament\Student\Resources\Profiles\Pages\EditProfile;
use App\Filament\Student\Resources\Profiles\Pages\IndexProfile;
use App\Filament\Student\Resources\Profiles\Pages\ViewProfile;
use App\Filament\Student\Resources\Profiles\Schemas\ProfileForm;
use App\Filament\Student\Resources\Profiles\Schemas\ProfileInfolist;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

class ProfileResource extends Resource
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $model = Student::class;


    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return ProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProfileInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => IndexProfile::route('/'),
            'view' => ViewProfile::route('/{record}'),
            'edit' => EditProfile::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth('student')->check();
    }

    public static function canView($record): bool
    {
        return auth('student')->check() && auth('student')->id() === $record->id;
    }

    public static function canEdit($record): bool
    {
        return auth('student')->check() && auth('student')->id() === $record->id;
    }

    public static function canCreate(): bool
    {
        return false; // Students can't create profiles
    }

    public static function canDelete($record): bool
    {
        return false; // Students can't delete their profiles
    }
}