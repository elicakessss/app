<?php

namespace App\Filament\Resources\Students\Tables;

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
 */
class StudentsTable
{
    /**
     * Configure the students table.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->size(50)
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                        ->grow(false),
                    
                    Stack::make([
                        TextColumn::make('name')
                            ->weight('medium')
                            ->searchable(),
                        
                        TextColumn::make('school_number')
                            ->getStateUsing(fn ($record) => $record->school_number ?? 'ID: ' . $record->id)
                            ->color('gray')
                            ->size('sm'),
                    ]),
                    
                    TextColumn::make('email')
                        ->searchable()
                        ->copyable()
                        ->icon('heroicon-o-envelope')
                        ->label('Email'),
                        
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->label('Registered'),

                ]),
            ])
            ->filters([
                //
            ])
            ->recordUrl(
                fn (Student $record): string => route('filament.admin.resources.students.edit', $record),
            )
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No students yet')
            ->emptyStateDescription('Students will appear here once they are registered.');
    }
}
