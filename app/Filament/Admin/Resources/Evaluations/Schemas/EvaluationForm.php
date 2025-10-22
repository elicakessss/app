<?php

namespace App\Filament\Admin\Resources\Evaluations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Evaluation Details')
                    ->schema([
                        Select::make('organization_id')
                            ->label('Organization')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user && $user->organization_id) {
                                    return \App\Models\Organization::where('id', $user->organization_id)
                                        ->pluck('name', 'id');
                                }
                                return \App\Models\Organization::pluck('name', 'id');
                            })
                            ->required(),
                        TextInput::make('year')
                            ->label('Academic Year')
                            ->required()
                            ->numeric()
                            ->default(date('Y')),
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
