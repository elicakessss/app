<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(125)
                            ->helperText('Enter a unique name for this role'),
                        TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(125)
                            ->helperText('Guard name (usually "web")'),
                    ])
                    ->columns(2),

                Section::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Select permissions for this role'),
                    ])
                    ->collapsible(),
            ]);
    }
}
