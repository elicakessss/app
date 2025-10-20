<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;

class OrganizationDetailsInfolist
{
    public static function configure(Infolist $infolist, $organization): Infolist
    {
        return $infolist->components([
            \Filament\Infolists\Components\Card::make()
                ->schema([
                    \Filament\Infolists\Components\Section::make('Organization Details')
                        ->schema([
                            ImageEntry::make('logo')
                                ->label('Logo')
                                ->height(64)
                                ->width(64)
                                ->alignCenter(),
                            TextEntry::make('name')->label('Organization Name'),
                            TextEntry::make('department.name')->label('Department'),
                            TextEntry::make('year')->label('Academic Year'),
                            TextEntry::make('description')->label('Description'),
                        ])
                        ->columns(1),
                ])
                ->columnSpanFull()
                ->extraAttributes(['class' => 'mb-6']),
        ]);
    }
}
