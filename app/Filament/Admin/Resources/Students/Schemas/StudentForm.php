<?php

namespace App\Filament\Admin\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

/**
 * Student form schema for Filament admin panel.
 */
class StudentForm
{
    /**
     * Configure the student form schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->label('School Number')
                    ->placeholder('e.g., 2024-001234')
                    ->helperText('Unique identifier for the student'),
                    
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Full Name'),
                        
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Email Address'),
                        
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->label('Password')
                    ->helperText('Minimum 8 characters'),
                        
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->same('password')
                    ->dehydrated(false)
                    ->label('Confirm Password'),
            ]);
    }
}
