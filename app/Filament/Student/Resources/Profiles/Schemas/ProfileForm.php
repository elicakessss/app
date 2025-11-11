<?php

namespace App\Filament\Student\Resources\Profiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Profile Image')
                            ->image()
                            ->directory('student-avatars')
                            ->maxSize(1024),

                        Textarea::make('description')
                            ->label('Bio')
                            ->rows(4)
                            ->maxLength(1000),
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Change Password')
                    ->description('Leave blank to keep current password')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->dehydrated(false)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if ($value && !Hash::check($value, auth('student')->user()->password)) {
                                            $fail('Current password is incorrect.');
                                        }
                                    };
                                },
                            ]),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->minLength(8)
                            ->same('password_confirmation'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
