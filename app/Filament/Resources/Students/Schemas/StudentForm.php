<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
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
                            ->maxLength(255)
                            ->label('School Number')
                            ->helperText('Optional student identification number'),
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
