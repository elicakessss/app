<?php

namespace App\Filament\Student\Resources\Certificates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Stack::make([
                    // Large file preview (image or icon)
                    \Filament\Tables\Columns\ImageColumn::make('file_path')
                        ->label('Certificate File')
                        ->height('200px')
                        ->width('100%')
                        ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'Certificate') . '&color=7F9CF5&background=EBF4FF')
                        ->extraImgAttributes(['style' => 'object-fit:contain;background:#fff;border-radius:8px;'])
                        ->visible(fn($record) => $record && $record->file_path),
                    // Certificate name
                    \Filament\Tables\Columns\TextColumn::make('name')
                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                        ->size('md')
                        ->label('Certificate Name')
                        ->alignCenter(),
                    // Date of issuance
                    \Filament\Tables\Columns\TextColumn::make('issued_at')
                        ->label('Date of Issuance')
                        ->date()
                        ->alignCenter(),
                ])->space(2)->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([])
            ->paginated([6, 12, 24])
            ->contentGrid([
                'md' => 1,
                'lg' => 2,
                'xl' => 3,
            ]);
    }
}
