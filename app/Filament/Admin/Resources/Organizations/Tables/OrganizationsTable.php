<?php

namespace App\Filament\Admin\Resources\Organizations\Tables;

use App\Models\Organization;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Organizations table configuration for Filament admin panel.
 */
class OrganizationsTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->columns([
				\Filament\Tables\Columns\ColumnGroup::make('Organization Details', [
					ImageColumn::make('logo')
						->label('Logo')
						->circular()
						->size(40)
						->defaultImageUrl(fn ($record) => $record->logo ? asset('storage/' . $record->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
						->grow(false),

					TextColumn::make('name')
						->label('Organization Name')
						->searchable()
						->weight('medium'),

					TextColumn::make('evaluations_count')
						->label('Evaluations')
						->counts('evaluations')
						->alignCenter()
						->badge()
						->color('info'),

					TextColumn::make('users_count')
						->label('Members')
						->counts('users')
						->alignCenter()
						->badge()
						->color('primary'),
				]),
				\Filament\Tables\Columns\ColumnGroup::make('Timestamps', [
					TextColumn::make('created_at')
						->label('Created At')
						->dateTime('M d, Y H:i'),

					TextColumn::make('updated_at')
						->label('Updated At')
						->dateTime('M d, Y H:i'),
				]),
			])
			->filters([
				// Add filters as needed
			])
			->recordUrl(
				fn (Organization $record): string => route('filament.admin.resources.organizations.edit', $record),
			)
			// Bulk actions removed
			->emptyStateHeading('No organizations yet')
			->emptyStateDescription('Organizations will appear here once they are created.');
	}
}
