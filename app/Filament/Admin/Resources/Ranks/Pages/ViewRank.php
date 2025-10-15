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
        return 'filament.admin.resources.ranks.pages.RankReport';
    }

    protected function getViewData(): array
    {
        $record = $this->getRecord();
        
        // Get all evaluations for this student/organization
        $evaluations = Evaluation::where('organization_id', $record->organization_id)
            ->where('student_id', $record->student_id)
            ->get()
            ->keyBy('evaluator_type');

        $questions = Evaluation::getAllQuestions();
        $groupedQuestions = $this->groupQuestions($questions);

        return [
            'record' => $record,
            'evaluations' => $evaluations,
            'questions' => $questions,
            'groupedQuestions' => $groupedQuestions,
        ];
    }

    /**
     * Group questions by domain and strand for organized display
     */
    public function groupQuestions(array $questions): array
    {
        if (empty($questions)) {
            return [];
        }

        $grouped = [];
        $domainTitles = [
            '1' => 'Domain 1: Paulinian Leadership as Social Responsibility',
            '2' => 'Domain 2: Paulinian Leadership as a Life of Service',
            '3' => 'Domain 3: Paulinian Leader as Leading by Example',
        ];

        foreach ($questions as $key => $text) {
            if ($key === 'length_of_service') {
                $grouped['Length of Service']['Service Duration'][$key] = $text;
                continue;
            }

            $parts = explode('_', $key);
            if (count($parts) >= 4) {
                $domainNum = $parts[1];
                $strandNum = $parts[3];

                $domainName = $domainTitles[$domainNum] ?? "Domain {$domainNum}";
                $strandName = "Strand {$strandNum}";

                $grouped[$domainName][$strandName][$key] = $text;
            }
        }

        return $grouped;
    }

    protected function getHeaderActions(): array
    {
        return [
            // No actions needed - read-only resource
        ];
    }
}
