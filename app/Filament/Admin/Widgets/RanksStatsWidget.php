<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Rank;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RanksStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getCards(): array
    {
        // Simple counts per rank. Using Eloquent here is sufficient for single-value counts.
        $gold = Rank::where('rank', 'gold')->count();
        $silver = Rank::where('rank', 'silver')->count();
        $bronze = Rank::where('rank', 'bronze')->count();

        return [
            Stat::make('Gold', $gold)
                ->icon('heroicon-s-star')
                ->chart($this->generateSparkline($gold))
                ->chartColor('success') // use Filament theme color
                ->description($this->generateDescription($this->generateSparkline($gold))),

            Stat::make('Silver', $silver)
                ->icon('heroicon-s-star')
                ->chart($this->generateSparkline($silver))
                ->chartColor('gray') // use Filament theme color
                ->description($this->generateDescription($this->generateSparkline($silver))),

            Stat::make('Bronze', $bronze)
                ->icon('heroicon-s-star')
                ->chart($this->generateSparkline($bronze))
                ->chartColor('warning') // use Filament theme color
                ->description($this->generateDescription($this->generateSparkline($bronze))),
        ];
    }

    /**
     * Generate a compact 6-point sparkline for the stat.
     *
     * @param int $value
     * @return array<string,int>
     */
    protected function generateSparkline(int $value): array
    {
        $points = [];

        for ($i = 5; $i >= 0; $i--) {
            $label = now()->subMonths($i)->format('M');
            $fraction = (6 - $i) / 6; // 1/6 .. 6/6
            $points[$label] = (int) round($value * $fraction);
        }

        return $points;
    }

    /**
     * Create a short description showing percent change between the last two
     * sparkline points. Returns a friendly string like '+12% since last month'.
     *
     * @param array<string,int> $sparkline
     * @return string
     */
    protected function generateDescription(array $sparkline): string
    {
        $values = array_values($sparkline);

        $last = array_pop($values);
        $prev = array_pop($values) ?? 0;

        if ($prev === 0) {
            return 'No previous data';
        }

        $change = ($last - $prev) / max(1, $prev) * 100;

        $sign = $change >= 0 ? '+' : '';

        return sprintf('%s%.0f%% since last month', $sign, $change);
    }
}
