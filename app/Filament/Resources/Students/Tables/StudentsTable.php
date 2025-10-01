<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                ImageColumn::make('profile_picture')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=Student&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable()
                    ->label('First Name'),

                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable()
                    ->label('Last Name'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->label('Email'),

                TextColumn::make('school_number')
                    ->searchable()
                    ->sortable()
                    ->label('School #')
                    ->placeholder('Not assigned')
                    ->toggleable(),

                TextColumn::make('bio')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        
                        return $state;
                    })
                    ->label('Biography')
                    ->toggleable()
                    ->placeholder('No bio provided'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Registered')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Updated')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_school_number')
                    ->label('Has School Number')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('school_number')),

                Filter::make('has_bio')
                    ->label('Has Biography')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('bio')),
            ])
            ->recordUrl(
                fn (Student $record): string => route('filament.admin.resources.students.edit', $record),
            )
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
