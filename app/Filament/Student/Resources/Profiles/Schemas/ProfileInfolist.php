<?php

namespace App\Filament\Student\Resources\Profiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        // Layout: 1/3 - 2/3 grid across the page. Left column is the profile card,
        // right column shows evaluations/certificates as a repeatable list.
        return $schema->components([
            Grid::make(3)->columnSpanFull()
                ->schema([
                    // LEFT: Profile card (avatar + basic info + bio)
                    Section::make('')
                        ->extraAttributes(['class' => 'space-y-2 divide-y divide-gray-100'])
                        ->schema([
                            ImageEntry::make('image')
                                ->label('')
                                ->hiddenLabel()
                                ->height(120)
                                ->width(120)
                                ->circular()
                                ->alignCenter()
                                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'Student') . '&background=ffffff'),

                            TextEntry::make('name')
                                ->label('Full Name')
                                ->hiddenLabel()
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)

                                ->extraAttributes(['class' => 'mb-1'])
                                ->alignCenter(),


                            TextEntry::make('email')
                                ->label('Email Address:')
                                ->copyable()
                                ->inlineLabel(),

                            TextEntry::make('school_number')
                                ->label('School Number:')
                                ->inlineLabel(),

                            TextEntry::make('description')
                                ->label('Bio')
                                ->hiddenLabel(),
                        ])
                        ->columnSpan(1),

                    // RIGHT: Participated organizations / certificates list
                    Section::make('Participated Organizations')
                        // ->extraAttributes(['class' => 'divide-y divide-gray-100'])
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
                                                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->organization->name ?? 'Organization') . '&color=7F9CF5&background=EBF4FF')
                                                ->columnSpan(1),

                                            Grid::make(1)
                                                ->schema([
                                                    \Filament\Infolists\Components\TextEntry::make('organization.name')
                                                        ->label('Organization: ')
                                                        ->inlineLabel()
                                                        ->extraEntryWrapperAttributes(['class' => 'py-3']),
                                                    \Filament\Infolists\Components\TextEntry::make('year')
                                                        ->label('Academic Year: ')
                                                        ->inlineLabel()
                                                        ->extraEntryWrapperAttributes(['class' => 'py-3']),
                                                    \Filament\Infolists\Components\TextEntry::make('pivot.position')
                                                        ->label('Position: ')
                                                        ->inlineLabel()
                                                        ->extraEntryWrapperAttributes(['class' => 'py-3']),
                                                ])
                                                ->columnSpan(2),
                                        ])

                                ])
            ])
            ->columnSpan(2),
                ]),
        ]);
    }
}
