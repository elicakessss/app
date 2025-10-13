<?php

namespace App\Filament\Admin\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Information')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label('Full Name'),
                            TextEntry::make('email')
                                ->label('Email Address')
                                ->copyable(),
                            TextEntry::make('school_number')
                                ->label('School Number')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('department.name')
                                ->label('Department')
                                ->badge()
                                ->color('info')
                                ->default('Not Assigned'),
                        ]),
                ]),

            Section::make('Account Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Account Created')
                                ->dateTime('M j, Y g:i A'),
                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('M j, Y g:i A'),
                        ]),
                    TextEntry::make('roles.name')
                        ->label('User Roles')
                        ->badge()
                        ->separator(', ')
                        ->color('primary')
                        ->default('No roles assigned'),
                ]),
        ]);
    }
}