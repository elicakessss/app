<?php

namespace App\Filament\Resources\Ranks\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('organization.year')
                    ->label('Academic Year')
                    ->sortable(),
                    
                TextColumn::make('student.organizations')
                    ->label('Position')
                    ->formatStateUsing(function ($record) {
                        $pivot = $record->student->organizations
                            ->where('id', $record->organization_id)
                            ->first()?->pivot;
                        return $pivot?->position ?? 'N/A';
                    }),
                    
                TextColumn::make('final_score')
                    ->label('Final Score')
                    ->numeric(decimalPlaces: 3)
                    ->sortable()
                    ->placeholder('Pending'),
                    
                BadgeColumn::make('rank')
                    ->label('Rank')
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
                    }),
                    
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'finalized',
                        'warning' => 'pending',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        return \App\Models\Organization::distinct('year')
                            ->orderBy('year', 'desc')
                            ->pluck('year', 'year')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->whereHas('organization', fn ($q) => 
                                $q->where('year', $data['value'])
                            );
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View Breakdown'),
            ])
            ->defaultSort('final_score', 'desc')
            ->emptyStateHeading('No rankings yet')
            ->emptyStateDescription('Rankings will appear here once evaluations are completed.');
    }
}
