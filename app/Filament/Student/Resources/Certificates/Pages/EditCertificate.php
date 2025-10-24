<?php

namespace App\Filament\Student\Resources\Certificates\Pages;

use App\Filament\Student\Resources\Certificates\CertificateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCertificate extends EditRecord
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

        public function getSubheading(): ?string
    {
        return 'Edit your certificate details.';
    }
}
