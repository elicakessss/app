<?php

namespace App\Filament\Student\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // Single Profile section with image and details arranged in a grid
            Section::make('Profile Section')
                ->columnSpanFull()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            ImageEntry::make('image')
                                ->label('Profile Picture')
                                ->hiddenLabel()
                                ->height(240)
                                ->width(240)
                                ->circular()
                                ->alignCenter()
                                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'Student') . '&background=ffffff')
                                ->columnSpan(1),

                            Grid::make(1)
                                ->schema([
                                    TextEntry::make('name')->label('Full Name: ')->inlineLabel(),
                                    TextEntry::make('email')->label('Email Address: ')->copyable()->inlineLabel(),
                                    TextEntry::make('school_number')->label('School Number: ')->badge()->color('gray')->inlineLabel(),
                                    TextEntry::make('description')->label('Bio')->inlineLabel(),
                                ])
                                ->columnSpan(2),
                        ]),
                ]),

            Section::make('Participated Organizations')
                ->columnSpanFull()
                ->schema([
                    \Filament\Infolists\Components\RepeatableEntry::make('evaluations')
                        ->label('Evaluations')
                        ->hiddenLabel()
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    ImageEntry::make('organization.logo')
                                        ->hiddenLabel()
                                        ->height(100)
                                        ->width(100)
                                        ->circular()
                                        ->alignCenter()
                                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->organization->name ?? 'Organization') . '&color=7F9CF5&background=EBF4FF'),
                                    Grid::make(1)
                                        ->schema([
                                            \Filament\Infolists\Components\TextEntry::make('organization.name')
                                                ->label('Organization: ')
                                                ->inlineLabel(),
                                            \Filament\Infolists\Components\TextEntry::make('year')
                                                ->label('Academic Year: ')
                                                ->inlineLabel(),
                                            \Filament\Infolists\Components\TextEntry::make('pivot.position')
                                                ->label('Position: ')
                                                ->inlineLabel(),
                                        ])
                                        ->columnSpan(2)
                                ])

                        ])
                ]),
        ]);
    }
}
