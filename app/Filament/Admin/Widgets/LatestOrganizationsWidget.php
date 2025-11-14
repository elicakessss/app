<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Collection;

class LatestOrganizationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Organizations';

    // Half-width on medium+ screens so it can sit side-by-side with the chart widget
    protected int | string | array $columnSpan = [
        'md' => 6,
        'xl' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(Organization::query()->withCount('evaluations'))
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->rounded()
                    ->height(48)
                    ->width(48)
                    ->extraAttributes(['class' => 'ring-1 ring-gray-100 dark:ring-gray-800']),

                TextColumn::make('name')
                    ->label('Name')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->limit(30),

                BadgeColumn::make('evaluations_count')
                    ->label('Evaluations')
                    ->sortable()
                    ->colors([
                        'danger' => fn($state): bool => (int) $state === 0,
                        'warning' => fn($state): bool => (int) $state > 0 && (int) $state <= 3,
                        'success' => fn($state): bool => (int) $state > 3,
                    ])
                    ->formatStateUsing(fn($state) => (int) $state)
                    ->extraAttributes(['class' => 'font-medium']),
            ])
            ->defaultSort('evaluations_count', 'desc')
            ->bulkActions([])
            ->filters([])
            ->headerActions([])
            ->paginated(false);
    }
}
