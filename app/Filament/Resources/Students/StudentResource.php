<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/**
 * Filament resource for managing students in the admin panel.
 */
class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    protected static ?int $navigationSort = 1;

    /**
     * Configure the form schema for creating/editing students.
     */
    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    /**
     * Configure the table for listing students.
     */
    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    /**
     * Get the relations that can be eager loaded.
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the pages available for this resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
