<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rank;
use Filament\Widgets\ChartWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class RanksChartWidget extends BaseWidget
{
    protected ?string $heading = 'Ranks Overview';

    // Half-width on medium+ screens so it can sit side-by-side with the organizations table
    protected int | string | array $columnSpan = [
        'md' => 6,
        'xl' => 6,
    ];

    // Prefer a bar chart
    protected ?string $chartType = 'bar';

    protected function getData(): array
    {
        // Use a safe query. Some MySQL versions or drivers can misinterpret the column name
        // `rank` (it collides with the RANK() window function). Attempt a DB-level group query
        // and fall back to an in-memory collection if the query fails.
        try {
            $grouped = DB::table('ranks')
                ->select('rank', DB::raw('count(*) as total'))
                ->groupBy('rank')
                ->pluck('total', 'rank')
                ->toArray();
        } catch (QueryException $e) {
            // Fallback: compute counts in PHP to avoid SQL parsing issues.
            $grouped = Rank::all()->groupBy('rank')->map(fn ($g) => $g->count())->toArray();
        }

        // Ensure consistent label order and prettier labels
        $labels = array_keys($grouped);
        $values = array_values($grouped);

        $backgrounds = array_map(fn($l) => match($l) {
            'gold' => 'rgba(245, 158, 11, 0.9)',
            'silver' => 'rgba(148, 163, 184, 0.9)',
            'bronze' => 'rgba(249, 115, 22, 0.9)',
            default => 'rgba(148, 163, 184, 0.6)',
        }, $labels);

        return [
            'datasets' => [
                [
                    'label' => 'Ranks',
                    'data' => $values,
                    'backgroundColor' => $backgrounds,
                    'borderColor' => array_map(fn($c) => str_replace('0.9', '1', $c), $backgrounds),
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                    'maxBarThickness' => 48,
                ],
            ],
            'labels' => array_map(fn($l) => ucfirst((string) $l), $labels),
            // ChartJS options passed through Filament's ChartWidget
            'options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'mode' => 'index',
                        'intersect' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => 'rgba(15, 23, 42, 0.04)',
                        ],
                    ],
                    'x' => [
                        'grid' => [
                            'display' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
