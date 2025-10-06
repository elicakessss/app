<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
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
                            ->searchable()
                            ->color('gray')
                            ->size('sm'),
                    ])->space(1),
                    
                    TextColumn::make('department.name')
                        ->searchable()
                        ->placeholder('No department assigned')
                        ->label('Department'),
                    
                    TextColumn::make('email')
                        ->searchable()
                        ->copyable()
                        ->icon('heroicon-o-envelope')
                        ->label('Email'),
                        
                    TextColumn::make('roles.name')
                        ->badge()
                        ->separator(',')
                        ->color('info')
                        ->label('Roles'),
                        
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->label('Joined'),
                ]),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Department'),
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Role'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they are created.');
    }
}
