<?php

namespace App\Filament\Student\Resources\Evaluations\Pages;

use App\Filament\Student\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Models\EvaluationScore;
use App\Models\Organization;
use App\Traits\HandlesEvaluationForms;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Self Evaluation Page for Students
 * 
 * Allows students to complete their self-evaluations using the same evaluation sheet layout
 */
class SelfEvaluate extends Page implements HasForms
{
    use InteractsWithForms, HandlesEvaluationForms;
    
    protected static string $resource = EvaluationResource::class;
    protected string $view = 'filament.student.resources.evaluations.pages.SelfEvaluationSheet';

    public Organization $organization;
    public ?Evaluation $evaluationEvent = null; // The evaluation event (year-scoped)
    public ?EvaluationScore $evaluationRecord = null; // Per-student evaluation answers
    public array $data = [];

    public function mount(Organization $organization): void
    {
        $studentId = auth('student')->id();

        // Find the latest evaluation event for this organization that includes this student
        $evaluation = Evaluation::where('organization_id', $organization->id)
            ->whereHas('students', function ($q) use ($studentId) {
                $q->where('students.id', $studentId);
            })
            ->orderByDesc('year')
            ->first();

        if (! $evaluation) {
            // Student is not enrolled in any evaluation for this organization
            $this->redirect(route('filament.student.resources.evaluations.index'));
            return;
        }

        $this->organization = $organization;
        $this->evaluationEvent = $evaluation;
        $this->loadExistingEvaluation();
    }

    /**
     * Load existing evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        if (! $this->evaluationEvent) {
            $this->evaluationRecord = null;
            $this->data = [];
            return;
        }

        $this->evaluationRecord = EvaluationScore::where([
            'evaluation_id' => $this->evaluationEvent->id,
            'student_id' => auth('student')->id(),
            'evaluator_type' => 'self',
        ])->first();

        if ($this->evaluationRecord) {
            $this->data = $this->evaluationRecord->answers ?? [];
        }
    }

    public function getTitle(): string|Htmlable
    {
        return "Self Evaluation - {$this->organization->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Complete your self-evaluation for {$this->organization->name}";
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * Save evaluation data to database
     */
    public function save(): void
    {
        $questions = EvaluationScore::getSelfQuestionsForStudents();
        if (! $this->validateAnswers($questions)) {
            return;
        }

        if ($this->evaluationRecord) {
            $this->updateExistingEvaluation($this->data);
        } else {
            $this->createNewEvaluation($this->data);
        }

        $this->sendSuccessNotification();
        $this->redirectToIndex();
    }

    /**
     * Create new evaluation record
     */
    protected function createNewEvaluation(array $data): void
    {
        if (! $this->evaluationEvent) {
            return;
        }

        EvaluationScore::create([
            'evaluation_id' => $this->evaluationEvent->id,
            'student_id' => auth('student')->id(),
            'evaluator_type' => 'self',
            'evaluator_id' => null,
            'answers' => $data,
        ]);
    }

    /**
     * Redirect back to organizations index
     */
    protected function redirectToIndex(): void
    {
        $this->redirect(route('filament.student.resources.evaluations.index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Self Evaluation')
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
     * Update an existing evaluation score record
     */
    protected function updateExistingEvaluation(array $data): void
    {
        if (! $this->evaluationRecord) {
            return;
        }
        $this->evaluationRecord->answers = $data;
        $this->evaluationRecord->save();
    }
}