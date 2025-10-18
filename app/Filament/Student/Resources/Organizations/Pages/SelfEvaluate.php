<?php

namespace App\Filament\Student\Resources\Organizations\Pages;

use App\Filament\Student\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
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
    
    protected static string $resource = OrganizationResource::class;
    protected string $view = 'filament.student.resources.organizations.pages.SelfEvaluationSheet';

    public Organization $organization;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    public function mount(Organization $organization): void
    {
        // Verify student belongs to this organization
        $studentId = auth('student')->id();
        if (!$organization->students()->where('student_id', $studentId)->exists()) {
            $this->redirect(route('filament.student.resources.organizations.index'));
            return;
        }

        $this->organization = $organization;
        $this->loadExistingEvaluation();
    }

    /**
     * Load existing evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $this->evaluation = Evaluation::where([
            'organization_id' => $this->organization->id,
            'student_id' => auth('student')->id(),
            'evaluator_type' => 'self',
        ])->first();

        if ($this->evaluation) {
            $this->data = $this->evaluation->answers ?? [];
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
        $questions = Evaluation::getSelfQuestionsForStudents();
        if (!$this->validateAnswers($questions)) {
            return;
        }

        $this->evaluation
            ? $this->updateExistingEvaluation($this->data)
            : $this->createNewEvaluation($this->data);

        $this->sendSuccessNotification();
        $this->redirectToIndex();
    }

    /**
     * Create new evaluation record
     */
    protected function createNewEvaluation(array $data): void
    {
        Evaluation::create([
            'organization_id' => $this->organization->id,
            'student_id' => auth('student')->id(),
            'evaluator_type' => 'self',
            'answers' => $data,
        ]);
    }

    /**
     * Redirect back to organizations index
     */
    protected function redirectToIndex(): void
    {
        $this->redirect(route('filament.student.resources.organizations.index'));
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
                ->url(route('filament.student.resources.organizations.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}