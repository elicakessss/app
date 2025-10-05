<?php

namespace App\Filament\Resources\Ranks\Schemas;

use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RankInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('student.name')
                                    ->label('Student Name'),
                                TextEntry::make('organization.name')
                                    ->label('Organization'),
                                TextEntry::make('organization.year')
                                    ->label('Academic Year'),
                                TextEntry::make('student.organizations')
                                    ->label('Position')
                                    ->formatStateUsing(function ($record) {
                                        $pivot = $record->student->organizations
                                            ->where('id', $record->organization_id)
                                            ->first()?->pivot;
                                        return $pivot?->position ?? 'N/A';
                                    }),
                            ]),
                    ]),
                    
                Section::make('Final Results')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('final_score')
                                    ->label('Final Score')
                                    ->numeric(decimalPlaces: 3)
                                    ->placeholder('Pending'),
                                TextEntry::make('rank')
                                    ->label('Rank')
                                    ->badge()
                                    ->color(fn ($record) => match($record->rank) {
                                        'gold' => 'warning',
                                        'silver' => 'gray',
                                        'bronze' => 'orange',
                                        'none' => 'danger',
                                        default => 'secondary'
                                    })
                                    ->formatStateUsing(fn (?string $state): string => match($state) {
                                        'gold' => 'Gold',
                                        'silver' => 'Silver', 
                                        'bronze' => 'Bronze',
                                        'none' => 'None',
                                        default => 'Pending'
                                    }),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state) => match($state) {
                                        'finalized' => 'success',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    })
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                            ]),
                    ]),
                    
                Section::make('Evaluation Breakdown')
                    ->schema([
                        TextEntry::make('breakdown')
                            ->label('')
                            ->formatStateUsing(function ($record) {
                                if (!$record->breakdown) {
                                    return 'No evaluation data available';
                                }
                                
                                $breakdown = $record->breakdown;
                                $output = '';
                                
                                foreach ($breakdown as $evaluatorType => $data) {
                                    $percentage = ($data['weight'] * 100) . '%';
                                    $output .= "**" . ucfirst($evaluatorType) . " ({$percentage}):** {$data['score']} → {$data['weighted_score']}\n";
                                }
                                
                                return $output;
                            })
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->breakdown)),
            ]);
    }
}
