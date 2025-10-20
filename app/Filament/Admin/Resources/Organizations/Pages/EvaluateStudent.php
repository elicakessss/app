<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
use App\Traits\HandlesEvaluationForms;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Evaluate Student Page
 * 
 * Provides dynamic evaluation forms based on evaluator type (adviser, peer, self).
 * Organizes questions by domain and strand for better user experience.
 */
class EvaluateStudent extends Page implements HasForms
{
    use InteractsWithForms, HandlesEvaluationForms;
    
    protected static string $resource = OrganizationResource::class;
    protected string $view = 'filament.admin.resources.organizations.pages.EvaluationSheet';

    public Organization $organization;
    public Student $student;
    public string $type;
    public ?Evaluation $evaluation = null;
    public array $data = [];
    public bool $isLocked = false;

    public function mount(Organization $organization, Student $student, string $type): void
    {
        $this->organization = $organization;
        $this->student = $student;
        $this->type = $type;
        $this->loadExistingEvaluation();
        $this->isLocked = $this->evaluation !== null; // Lock if already submitted
    }

    /**
     * Load existing evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $query = Evaluation::where([
            'organization_id' => $this->organization->id,
            'student_id' => $this->student->id,
            'evaluator_type' => $this->type,
        ]);

        // For peer evaluations, also match the evaluator_id
        if ($this->type === 'peer') {
            $query->where('evaluator_id', auth('student')->id());
        } else {
            $query->whereNull('evaluator_id');
        }

        $this->evaluation = $query->first();

        if ($this->evaluation) {
            $this->data = $this->evaluation->answers ?? [];
        }
    }

    public function getTitle(): string|Htmlable
    {
        $evaluatorTitle = ucfirst($this->type);
        return "{$evaluatorTitle} Evaluation - {$this->student->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Organization: {$this->organization->name}";
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
        $questions = Evaluation::getQuestionsForEvaluator($this->type);
        if (!$this->validateAnswers($questions)) {
            return;
        }

        $this->evaluation
            ? $this->updateExistingEvaluation($this->data)
            : $this->createNewEvaluation($this->data);

        $this->sendSuccessNotification();
        $this->redirectToOrganization();
    }

    /**
     * Create new evaluation record
     */
    protected function createNewEvaluation(array $data): void
    {
        Evaluation::create([
            'organization_id' => $this->organization->id,
            'student_id' => $this->student->id,
            'evaluator_type' => $this->type,
            'evaluator_id' => $this->type === 'peer' ? auth('student')->id() : null, // Set evaluator_id for peer evaluations
            'answers' => $data,
        ]);
    }

    /**
     * Redirect back to organization view
     */
    protected function redirectToOrganization(): void
    {
        $this->redirect(route('filament.admin.resources.organizations.view', [
            'record' => $this->organization->id
        ]));
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (! $this->isLocked) {
            $actions[] = Action::make('save')
                ->label('Submit Evaluation')
                ->action('save')
                ->requiresConfirmation()
                ->modalHeading('Submit Evaluation?')
                ->modalDescription('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.')
                ->keyBindings(['mod+s'])
                ->color('success');
        }
        return $actions;
    }
}
