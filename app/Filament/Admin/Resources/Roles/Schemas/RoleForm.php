<?php

namespace App\Filament\Admin\Resources\Roles\Schemas;

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
                            ->label('Role Name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(125)
                            ->disabled(fn ($record) => $record && $record->name === 'Admin')
                            ->helperText(fn ($record) => 
                                $record && $record->name === 'Admin' 
                                    ? 'Admin role name cannot be changed to prevent system lockout'
                                    : ' '
                            ),
                        TextInput::make('guard_name')
                            ->default('web')
                            ->label('Guard Name')
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
