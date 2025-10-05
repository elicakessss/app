<?php

namespace App\Filament\Resources\Ranks;

use App\Filament\Resources\Ranks\Pages\ListRanks;
use App\Filament\Resources\Ranks\Pages\ViewRank;
use App\Filament\Resources\Ranks\Schemas\RankInfolist;
use App\Filament\Resources\Ranks\Tables\RanksTable;
use App\Models\Rank;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RankResource extends Resource
{
    protected static ?string $model = Rank::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Rankings';

    protected static ?string $modelLabel = 'Rank';

    protected static ?string $pluralModelLabel = 'Rankings';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'student.name';

    // Make resource read-only
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return RankInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RanksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRanks::route('/'),
            'view' => ViewRank::route('/{record}'),
        ];
    }
}
