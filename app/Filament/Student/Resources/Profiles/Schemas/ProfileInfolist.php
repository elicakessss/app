<?php

namespace App\Filament\Student\Resources\Profiles\Schemas;

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
                            TextEntry::make('organizations.name')
                                ->label('Organizations')
                                ->badge()
                                ->separator(', ')
                                ->color('info')
                                ->default('No organizations'),
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
                ]),
            Section::make('Participated Evaluations')
                ->schema([
                    \Filament\Infolists\Components\RepeatableEntry::make('evaluations')
                        ->label('Evaluations')
                        ->table([
                            \Filament\Infolists\Components\TextEntry::make('name')->label('Evaluation Name'),
                            \Filament\Infolists\Components\TextEntry::make('organization.name')->label('Organization'),
                            \Filament\Infolists\Components\TextEntry::make('year')->label('Year'),
                            \Filament\Infolists\Components\TextEntry::make('pivot.position')->label('Role/Position'),
                        ])
                ]),
        ]);
    }
}