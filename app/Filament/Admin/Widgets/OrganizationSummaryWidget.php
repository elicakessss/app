<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use App\Models\Student;
use App\Models\Rank;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class OrganizationSummaryWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 2;
    
    protected function getTableHeading(): string
    {
        return '📊 Organization Summary';
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Organization::query()
                    ->withCount(['students'])
                    ->with(['ranks' => function ($query) {
                        $query->where('status', 'finalized');
                    }])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Organization')
                    ->weight('medium')
                    ->searchable(),
                    
                TextColumn::make('year')
                    ->label('Academic Year')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        $nextYear = $state + 1;
                        return "{$state}-{$nextYear}";
                    }),
                    
                TextColumn::make('students_count')
                    ->label('👨‍🎓 Students')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('evaluated_count')
                    ->label('✅ Evaluated')
                    ->getStateUsing(function ($record) {
                        return $record->ranks()->where('status', 'finalized')->count();
                    })
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('average_score')
                    ->label('📈 Avg Score')
                    ->getStateUsing(function ($record) {
                        $avg = $record->ranks()->where('status', 'finalized')->avg('final_score');
                        return $avg ? number_format($avg, 2) : 'N/A';
                    })
                    ->alignCenter(),
                    
                BadgeColumn::make('top_rank')
                    ->label('🏆 Top Rank')
                    ->getStateUsing(function ($record) {
                        $topRank = $record->ranks()
                            ->where('status', 'finalized')
                            ->orderBy('final_score', 'desc')
                            ->first();
                        return $topRank?->rank ?? 'none';
                    })
                    ->colors([
                        'warning' => 'gold',
                        'gray' => 'silver',
                        'orange' => 'bronze', 
                        'danger' => 'none',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->defaultPaginationPageOption(5);
    }
}
