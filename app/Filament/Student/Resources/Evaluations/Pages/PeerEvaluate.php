<?php

namespace App\Filament\Student\Resources\Evaluations\Pages;

use App\Filament\Student\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
use App\Models\EvaluationScore;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Peer Evaluation Page for Students
 * 
 * Allows students to complete peer evaluations using the same evaluation sheet layout
 */
class PeerEvaluate extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = EvaluationResource::class;
    protected string $view = 'filament.student.resources.evaluations.pages.PeerEvaluationSheet';

    public Organization $organization;
    public Student $targetStudent;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    /**
     * Mount the page and verify student belongs to organization
     */
    public function mount(Organization $organization, Student $student): void
    {
        $studentId = auth('student')->id();

        // Find an evaluation for this organization that includes the current student as a participant
        $evaluation = Evaluation::where('organization_id', $organization->id)
            ->whereHas('students', function ($q) use ($studentId) {
                $q->where('students.id', $studentId);
            })
            ->orderByDesc('year')
            ->first();

        if (! $evaluation) {
            $this->redirect(route('filament.student.resources.evaluations.index'));
            return;
        }

        $this->organization = $organization;
        $this->evaluation = $evaluation;
        $this->targetStudent = $student;
        $this->loadExistingEvaluation();
    }

    /**
     * Load existing peer evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        if (! isset($this->evaluation) || ! $this->evaluation) {
            $this->data = [];
            return;
        }

        $this->evaluationRecord = EvaluationScore::where([
            'evaluation_id' => $this->evaluation->id,
            'student_id' => $this->targetStudent->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => auth('student')->id(),
        ])->first();

        if ($this->evaluationRecord) {
            $this->data = $this->evaluationRecord->answers ?? [];
        }
    }

    /**
     * Get the page title
     */
    public function getTitle(): string|Htmlable
    {
        return "Peer Evaluation - {$this->targetStudent->name} ({$this->organization->name})";
    }

    /**
     * Get the page subheading
     */
    public function getSubheading(): string|Htmlable|null
    {
        return "Complete your peer evaluation for {$this->targetStudent->name} in {$this->organization->name}";
    }

    /**
     * Save peer evaluation data to database
     */
    public function save(): void
    {
        $questions = EvaluationScore::getPeerQuestionsForStudents();
        if ($this->evaluationRecord) {
            $this->updateExistingEvaluation($this->data);
        } else {
            $this->createNewEvaluation($this->data);
        }
        $this->redirectToIndex();
    }

    // ...existing code...

    /**
     * Create a new peer evaluation
     */
    protected function createNewEvaluation(array $data): void
    {
        if (! isset($this->evaluation) || ! $this->evaluation) {
            return;
        }

        EvaluationScore::create([
            'evaluation_id' => $this->evaluation->id,
            'student_id' => $this->targetStudent->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => auth('student')->id(),
            'answers' => $data,
        ]);
    }

    /**
     * Update an existing peer evaluation record
     */
    protected function updateExistingEvaluation(array $data): void
    {
        if (! $this->evaluationRecord) {
            return;
        }
        $this->evaluationRecord->answers = $data;
        $this->evaluationRecord->save();
    }

    /**
     * Redirect to the organizations index
     */
    protected function redirectToIndex(): void
    {
        $this->redirect(route('filament.student.resources.evaluations.index'));
    }

    /**
     * Get header actions for the page
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Peer Evaluation')
                ->action('save')
                ->keyBindings(['mod+s'])
                ->color('success')
                ->icon('heroicon-o-check'),
            Action::make('back')
                ->label('Back to Organizations')
                ->url(route('filament.student.resources.evaluations.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    /**
     * Group questions by domain and strand for display in Blade view
     */
    public function groupQuestions(array $questions): array
    {
        $grouped = [];
        foreach ($questions as $questionKey => $question) {
            $domain = $question['domain'] ?? 'General';
            $strand = $question['strand'] ?? 'General';
            if (!isset($grouped[$domain])) {
                $grouped[$domain] = [];
            }
            if (!isset($grouped[$domain][$strand])) {
                $grouped[$domain][$strand] = [];
            }
            $grouped[$domain][$strand][$questionKey] = $question['text'] ?? $questionKey;
        }
        return $grouped;
    }
}
