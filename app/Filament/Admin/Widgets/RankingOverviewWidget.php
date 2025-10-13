<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rank;
use Filament\Widgets\ChartWidget;

class RankingOverviewWidget extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 2;
    
    protected ?string $heading = '🏅 Ranking Distribution';
    
    protected ?string $description = 'Gold, Silver, Bronze distribution across organizations';
    
    protected ?string $maxHeight = '300px';
    
    protected function getData(): array
    {
        $goldCount = Rank::where('rank', 'gold')->where('status', 'finalized')->count();
        $silverCount = Rank::where('rank', 'silver')->where('status', 'finalized')->count();
        $bronzeCount = Rank::where('rank', 'bronze')->where('status', 'finalized')->count();
        $noneCount = Rank::where('rank', 'none')->where('status', 'finalized')->count();
        
        return [
            'datasets' => [
                [
                    'label' => 'Rankings',
                    'data' => [$goldCount, $silverCount, $bronzeCount, $noneCount],
                    'backgroundColor' => [
                        '#FFD700', // Gold
                        '#C0C0C0', // Silver  
                        '#CD7F32', // Bronze
                        '#9CA3AF', // Gray for None
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                "Gold ({$goldCount})",
                "Silver ({$silverCount})", 
                "Bronze ({$bronzeCount})",
                "Unranked ({$noneCount})"
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
