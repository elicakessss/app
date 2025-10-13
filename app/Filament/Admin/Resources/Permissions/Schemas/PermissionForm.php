<?php

namespace App\Filament\Admin\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Permission Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(125)
                            ->helperText('Enter a unique permission name (e.g., "view users", "edit posts")'),
                        TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(125)
                            ->helperText('Guard name (usually "web")'),
                    ])
                    ->columns(2),
            ]);
    }
}
