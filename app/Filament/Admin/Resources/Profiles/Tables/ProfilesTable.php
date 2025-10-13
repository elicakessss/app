<?php

namespace App\Filament\Admin\Resources\Profiles\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Account Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // No filters needed for personal profile
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit Profile'),
            ])
            ->toolbarActions([
                // No bulk actions for profile management
            ])
            ->emptyStateHeading('Profile Information')
            ->emptyStateDescription('Your account profile information will appear here.');
    }
}
