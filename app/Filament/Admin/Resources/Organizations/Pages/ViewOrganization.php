<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Filament\Admin\Resources\Organizations\Schemas\OrganizationDetailsInfolist;
use App\Filament\Admin\Resources\Organizations\Schemas\PeerDetailsInfolist;
use App\Filament\Admin\Resources\Organizations\RelationManagers\StudentsRelationManager;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    // Removed getInfolist() to let the resource's getViewSchema() handle InfoList rendering

    public function getEvaluationStatus(int $studentId, string $evaluatorType): ?string
    {
        $evaluation = Evaluation::where('organization_id', $this->record->id)
            ->where('student_id', $studentId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        return $evaluation ? 'Done' : null;
    }
}