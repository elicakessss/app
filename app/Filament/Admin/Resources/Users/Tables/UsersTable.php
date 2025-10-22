<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Users Table Configuration
 * 
 * Displays users with their avatar, contact information, department, and roles
 * Uses split layout for better visual organization
 */
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->weight('semibold')
                    ->searchable(),

                TextColumn::make('school_number')
                    ->label('ID Number')
                    ->searchable()
                    ->color('gray')
                    ->size('sm'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->label('Email'),

                TextColumn::make('organization.name')
                    ->placeholder('No organization assigned')
                    ->label('Organization'),

                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->label('Roles'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Joined'),
            ])
            ->filters([
                SelectFilter::make('organization')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Organization'),
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Role'),
            ])
            // Actions column removed
            ->emptyStateHeading('No users yet')
            ->emptyStateDescription('Users will appear here once they are created.');
    }
}
