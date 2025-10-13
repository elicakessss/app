<?php

namespace App\Filament\Admin\Resources\Organizations\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    ImageColumn::make('logo')
                        ->circular()
                        ->size(80)
                        ->defaultImageUrl(function ($record) {
                            return 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF';
                        })
                        ->grow(false),
                    
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->wrap(),
                        
                        TextColumn::make('user.name')
                            ->label('Adviser')
                            ->color('gray')
                            ->prefix('Adviser: ')
                            ->size('sm'),
                        
                        Split::make([
                            TextColumn::make('year')
                                ->badge()
                                ->color('primary')
                                ->formatStateUsing(fn ($state) => $state . '-' . ($state + 1))
                                ->grow(false),
                            
                            TextColumn::make('students_count')
                                ->counts('students')
                                ->badge()
                                ->color('success')
                                ->suffix(' students')
                                ->grow(false),
                        ])->from('sm'),
                    ])->space(1),
                ])
                ->from('md'),
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),

                SelectFilter::make('year')
                    ->label('Academic Year')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
                            $nextYear = $i + 1;
                            $years[$i] = "$i-$nextYear";
                        }
                        return $years;
                    }),
            ])
            ->recordActions([
                // Actions hidden as requested
            ])
            ->bulkActions([
                // Removed bulk actions to eliminate checkboxes
            ])
            ->searchable()
            ->paginated([10, 25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 1,
                'lg' => 2,
                'xl' => 3,
            ]);
    }
}
