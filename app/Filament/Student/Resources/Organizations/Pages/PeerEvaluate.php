<?php

namespace App\Filament\Student\Resources\Organizations\Pages;

use App\Filament\Student\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
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
    
    protected static string $resource = OrganizationResource::class;
    protected string $view = 'filament.student.resources.organizations.pages.PeerEvaluationSheet';

    public Organization $organization;
    public Student $targetStudent;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    public function mount(Organization $organization, Student $student): void
    {
        // Verify student belongs to this organization
        $studentId = auth('student')->id();
        if (!$organization->students()->where('student_id', $studentId)->exists()) {
            $this->redirect(route('filament.student.resources.organizations.index'));
            return;
        }

        $this->organization = $organization;
        $this->targetStudent = $student;
        $this->loadExistingEvaluation();
    }

    /**
     * Load existing peer evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $this->evaluation = Evaluation::where([
            'organization_id' => $this->organization->id,
            'student_id' => $this->targetStudent->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => auth('student')->id(),
        ])->first();

        if ($this->evaluation) {
            $this->data = $this->evaluation->answers ?? [];
        }
    }

    public function getTitle(): string|Htmlable
    {
        return "Peer Evaluation - {$this->targetStudent->name} ({$this->organization->name})";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Complete your peer evaluation for {$this->targetStudent->name} in {$this->organization->name}";
    }

    /**
     * Save peer evaluation data to database
     */
    public function save(): void
    {
        $questions = Evaluation::getPeerQuestionsForStudents();
        // Add validation logic as needed
        $this->evaluation
            ? $this->updateExistingEvaluation($this->data)
            : $this->createNewEvaluation($this->data);

        // Add notification and redirect logic as needed
        $this->redirectToIndex();
    }

    protected function createNewEvaluation(array $data): void
    {
        Evaluation::create([
            'organization_id' => $this->organization->id,
            'student_id' => $this->targetStudent->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => auth('student')->id(),
            'answers' => $data,
        ]);
    }

    protected function redirectToIndex(): void
    {
        $this->redirect(route('filament.student.resources.organizations.index'));
    }

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
                ->url(route('filament.student.resources.organizations.index'))
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
