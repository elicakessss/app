<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255)
                            ->label('First Name'),
                            
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Last Name'),
                            
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->label('Email Address'),
                            
                        TextInput::make('school_number')
                            ->required()
                            ->maxLength(255)
                            ->label('School Number')
                            ->helperText('Student identification number'),
                            
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
                    ])
                    ->columns(2),
                
                Section::make('Additional Information')
                    ->schema([
                        FileUpload::make('profile_picture')
                            ->image()
                            ->imageEditor()
                            ->maxSize(1024)
                            ->directory('student-profiles')
                            ->label('Profile Picture')
                            ->helperText('Upload a profile picture (max 1MB)'),
                            
                        Textarea::make('bio')
                            ->rows(4)
                            ->maxLength(1000)
                            ->label('Biography')
                            ->helperText('Tell us about yourself'),
                    ])
                    ->columns(1),
            ]);
    }
}
