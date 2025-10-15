<?php

namespace App\Filament\Admin\Resources\Evaluations\Tables;

use Filament\Tables\Table;

/**
 * Evaluations Table Configuration
 * 
 * Currently unused - evaluations are managed through custom pages
 * This file is kept for potential future table-based evaluation management
 */
class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns will be implemented when needed
            ])
            ->filters([
                // Table filters will be implemented when needed
            ]);
    }
}
