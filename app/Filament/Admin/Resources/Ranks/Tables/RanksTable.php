<?php

namespace App\Filament\Admin\Resources\Ranks\Tables;

use App\Models\Organization;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('student.avatar')
                        ->circular()
                        ->size(50)
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->student->name) . '&color=7F9CF5&background=EBF4FF')
                        ->grow(false),
                    
                    Stack::make([
                        TextColumn::make('student.name')
                            ->weight('medium')
                            ->searchable(),
                        
                        TextColumn::make('position')
                            ->getStateUsing(function ($record) {
                                $pivot = $record->student->organizations()
                                    ->where('organization_id', $record->organization_id)
                                    ->first()?->pivot;
                                return $pivot?->position ?? 'Member';
                            })
                            ->color('gray')
                            ->size('sm'),
                    ]),
                    
                    Stack::make([
                        TextColumn::make('organization.name')
                            ->searchable(),
                        
                        TextColumn::make('organization.year')
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'N/A';
                                $nextYear = $state + 1;
                                return "{$state}-{$nextYear}";
                            }),
                    ]),
                            
                    TextColumn::make('final_score')
                        ->numeric(decimalPlaces: 3)
                        ->placeholder('Pending')
                        ->label('Final Score'),
                        
                    BadgeColumn::make('rank')
                        ->colors([
                            'warning' => 'gold',
                            'gray' => 'silver', 
                            'orange' => 'bronze',
                            'danger' => 'none',
                        ])
                        ->formatStateUsing(fn (?string $state): string => match($state) {
                            'gold' => 'Gold',
                            'silver' => 'Silver',
                            'bronze' => 'Bronze',
                            'none' => 'None',
                            default => 'Pending'
                        })
                        ->label('Rank'),
                        
                    BadgeColumn::make('status')
                        ->colors([
                            'success' => 'finalized',
                            'warning' => 'pending',
                        ])
                        ->formatStateUsing(fn (string $state): string => ucfirst($state))
                        ->label('Status'),
                ]),
            ])
            ->filters([
                SelectFilter::make('rank')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                        'bronze' => 'Bronze',
                        'none' => 'None',
                    ]),
                    
                SelectFilter::make('status')
                    ->options([
                        'finalized' => 'Finalized',
                        'pending' => 'Pending',
                    ]),
                    
                SelectFilter::make('organization_id')
                    ->relationship('organization', 'name')
                    ->label('Organization'),
                    
                SelectFilter::make('year')
                    ->options(function () {
                        return Organization::distinct('year')
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->whereHas('organization', function ($q) use ($data) {
                                $q->where('year', $data['value']);
                            });
                        }
                    }),
            ])
            ->emptyStateHeading('No rankings yet')
            ->emptyStateDescription('Rankings will appear here once evaluations are completed.');
    }
}
