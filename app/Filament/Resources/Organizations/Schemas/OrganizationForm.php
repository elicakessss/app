<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\Department;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->placeholder('e.g., Student Council 2024-2025'),

                Select::make('year')
                    ->label('Academic Year')
                    ->required()
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 5; $i <= $currentYear + 2; $i++) {
                            $nextYear = $i + 1;
                            $years[$i] = "$i-$nextYear";
                        }
                        return $years;
                    })
                    ->default(date('Y')),

                Select::make('department_id')
                    ->label('Department')
                    ->options(Department::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                FileUpload::make('logo')
                    ->label('Organization Logo')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->directory('organization-logos')
                    ->helperText('Upload organization logo (max 2MB)')
                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml']),
                
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Brief description of the organization and its purpose'),
            ]);
    }
}
