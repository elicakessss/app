<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                TextInput::make('score')
                    ->numeric(),
                TextInput::make('criteria'),
                Textarea::make('comments')
                    ->columnSpanFull(),
                DatePicker::make('evaluation_date')
                    ->required(),
            ]);
    }
}
