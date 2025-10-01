<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Organization Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Department of Information Technology'),
                
                TextInput::make('code')
                    ->label('Organization Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('e.g., DIT2024')
                    ->alphaDash(),
                
                Select::make('year')
                    ->label('Academic Year')
                    ->required()
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(date('Y')),
                
                Toggle::make('is_active')
                    ->label('Active Status')
                    ->default(true)
                    ->helperText('Toggle to activate/deactivate this organization'),
                
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Brief description of the organization and its purpose'),
            ]);
    }
}
