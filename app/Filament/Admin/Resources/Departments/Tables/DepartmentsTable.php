<?php

namespace App\Filament\Admin\Resources\Departments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Departments Table Configuration
 * 
 * Displays departments with their organizations count and basic information
 */
class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Department Name')
                    ->searchable()
                    ->weight(FontWeight::SemiBold),

                TextColumn::make('abbreviation')
                    ->label('Abbreviation')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('organizations_count')
                    ->label('Organizations')
                    ->counts('organizations')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No departments yet')
            ->emptyStateDescription('Departments will appear here once they are created.');
    }
}
