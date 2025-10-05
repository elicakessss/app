<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getEvaluationStatus(int $studentId, string $evaluatorType): ?string
    {
        $evaluation = Evaluation::where('organization_id', $this->record->id)
            ->where('student_id', $studentId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        return $evaluation ? 'Done' : null;
    }
}