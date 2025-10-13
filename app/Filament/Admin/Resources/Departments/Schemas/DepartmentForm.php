<?php

namespace App\Filament\Admin\Resources\Departments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Department Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., School of Information Technology and Engineering'),
                
                TextInput::make('abbreviation')
                    ->label('Abbreviation')
                    ->maxLength(20)
                    ->placeholder('e.g., SITE')
                    ->alphaDash(),
                
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Brief description of the department'),
            ]);
    }
}
