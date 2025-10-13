<?php

namespace App\Filament\Admin\Resources\Ranks\Pages;

use App\Filament\Admin\Resources\Ranks\RankResource;
use App\Models\Evaluation;
use Filament\Resources\Pages\ViewRecord;

class ViewRank extends ViewRecord
{
    protected static string $resource = RankResource::class;
    
    public function getView(): string
    {
        return 'filament.admin.resources.ranks.pages.view-rank';
    }

    protected function getViewData(): array
    {
        $record = $this->getRecord();
        
        // Get all evaluations for this student/organization
        $evaluations = Evaluation::where('organization_id', $record->organization_id)
            ->where('student_id', $record->student_id)
            ->get()
            ->keyBy('evaluator_type');

        return [
            'record' => $record,
            'evaluations' => $evaluations,
            'questions' => Evaluation::getAllQuestions(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed - read-only resource
        ];
    }
}
