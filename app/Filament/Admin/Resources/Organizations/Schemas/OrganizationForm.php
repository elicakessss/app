<?php

namespace App\Filament\Admin\Resources\Organizations\Schemas;

use App\Models\Department;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Organization Details')
                    ->schema([
                        Hidden::make('user_id')
                            ->default(auth()->id()),
                        // Left column: logo
                        FileUpload::make('logo')
                            ->label('Organization Logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('organization-logos')
                            ->helperText('Upload organization logo (max 2MB)')
                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                            ->columnSpan(1),
                        // Right column: details
                        \Filament\Schemas\Components\Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Organization Name')
                                    ->required()
                                    ->maxLength(255),
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
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
