<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Models\Student;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('position')
                    ->label('Position')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Team Leader, Secretary, Member'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('school_number')
                    ->label('School Number')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),

                TextColumn::make('pivot.created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Student')
                    ->form(fn (AttachAction $action): array => [
                        Select::make('recordId')
                            ->label('Student')
                            ->options(Student::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->required(),
                        
                        TextInput::make('position')
                            ->label('Position')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Team Leader, Secretary, Member'),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('position')
                            ->label('Position')
                            ->required()
                            ->maxLength(255),
                    ]),
                DetachAction::make()
                    ->label('Remove'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
