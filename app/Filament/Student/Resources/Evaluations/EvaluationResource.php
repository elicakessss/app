<?php

namespace App\Filament\Student\Resources\Evaluations;

use App\Filament\Student\Resources\Evaluations\Pages\ListEvaluations;
use App\Filament\Student\Resources\Evaluations\Pages\SelfEvaluate;
use App\Filament\Student\Resources\Evaluations\Pages\PeerEvaluate;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;


/**
 * Student Evaluations Resource
 *
 * Provides the student-facing Evaluations list and evaluation actions (self/peer).
 */
class EvaluationResource extends Resource
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Evaluations';

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
        return false; // Students can't create evaluation events
    }

    public static function canEdit($record): bool
    {
        return false; // Students can't edit evaluation events
    }

    public static function canDelete($record): bool
    {
        return false; // Students can't delete evaluation events
    }

    public static function canView($record): bool
    {
        return false; // No individual view needed
    }

    // Table is handled by ListEvaluations page

    public static function getEloquentQuery(): Builder
    {
        // Only show organizations that the current student belongs to (used to build evaluation tasks)
        return parent::getEloquentQuery()
            ->whereHas('students', function (Builder $query) {
                $query->where('student_id', auth('student')->id());
            })
            ->with(['organization', 'students']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluations::route('/'),
            'self-evaluate' => SelfEvaluate::route('/{evaluation}/self-evaluate'),
            'peer-evaluate' => PeerEvaluate::route('/{evaluation}/{student}/peer-evaluate'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}