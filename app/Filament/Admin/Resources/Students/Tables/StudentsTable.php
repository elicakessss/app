<?php

namespace App\Filament\Admin\Resources\Students\Tables;

use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;

/**
 * Students table configuration for Filament admin panel.
 * Displays student information with search and bulk actions.
 */
class StudentsTable
{
    /**
     * Configure the students table with proper columns and actions.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label(' ')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->grow(false)
                    ->alignCenter(),

                TextColumn::make('name')
                    ->weight('medium')
                    ->searchable(),

                TextColumn::make('school_number')
                    ->searchable()
                    ->label('ID Number')
                    ->color('gray')
                    ->size('sm'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->label('Email'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Registered'),
            ])
            ->recordUrl(
                fn (Student $record): string => route('filament.admin.resources.students.edit', $record),
            )
            ->emptyStateHeading('No students yet')
            ->emptyStateDescription('Students will appear here once they are registered.');
    }
}
