<?php

namespace App\Filament\Student\Resources\Certificates\Schemas;

use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Certificate Name')
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('issued_at')
                    ->label('Issued At'),
                \Filament\Forms\Components\FileUpload::make('file_path')
                    ->label('Certificate File')
                    ->directory('certificates')
                    ->preserveFilenames(),
            ]);
    }
}
