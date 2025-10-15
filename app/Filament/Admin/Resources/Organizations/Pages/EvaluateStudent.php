<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
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
    use InteractsWithForms;
    
    protected static string $resource = OrganizationResource::class;
    protected string $view = 'filament.admin.resources.organizations.pages.EvaluationSheet';

    public Organization $organization;
    public Student $student;
    public string $type;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    /** Domain number to title mapping */
    private const DOMAIN_TITLES = [
        '1' => 'Domain 1: Paulinian Leadership as Social Responsibility',
        '2' => 'Domain 2: Paulinian Leadership as a Life of Service',
        '3' => 'Domain 3: Paulinian Leader as Leading by Example',
    ];

    public function mount(Organization $organization, Student $student, string $type): void
    {
        $this->organization = $organization;
        $this->student = $student;
        $this->type = $type;
        $this->loadExistingEvaluation();
    }

    /**
     * Load existing evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $this->evaluation = Evaluation::where([
            'organization_id' => $this->organization->id,
            'student_id' => $this->student->id,
            'evaluator_type' => $this->type,
        ])->first();

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
     * Group questions by domain and strand for organized display
     */
    public function groupQuestions(array $questions): array
    {
        if (empty($questions)) {
            return [];
        }

        $grouped = [];

        foreach ($questions as $key => $text) {
            if ($key === 'length_of_service') {
                $grouped['Length of Service']['Service Duration'][$key] = $text;
                continue;
            }

            $this->categorizeQuestion($key, $text, $grouped);
        }

        return $grouped;
    }

    /**
     * Categorize a single question into its domain and strand
     */
    public function categorizeQuestion(string $key, string $text, array &$grouped): void
    {
        $parts = explode('_', $key);

        if (count($parts) >= 4) {
            $domainNum = $parts[1];
            $strandNum = $parts[3];

            $domainName = self::DOMAIN_TITLES[$domainNum] ?? "Domain {$domainNum}";
            $strandName = "Strand {$strandNum}";

            $grouped[$domainName][$strandName][$key] = $text;
        }
    }

    /**
     * Save evaluation data to database
     */
    public function save(): void
    {
        if (!$this->validateAnswers()) {
            return;
        }

        $this->evaluation
            ? $this->updateExistingEvaluation($this->data)
            : $this->createNewEvaluation($this->data);

        $this->sendSuccessNotification();
        $this->redirectToOrganization();
    }

    /**
     * Validate that all required questions have answers
     */
    private function validateAnswers(): bool
    {
        $questions = Evaluation::getQuestionsForEvaluator($this->type);
        $unansweredQuestions = array_filter(
            array_keys($questions),
            fn($key) => !isset($this->data[$key]) || $this->data[$key] === '' || $this->data[$key] === null
        );

        if (!empty($unansweredQuestions)) {
            Notification::make()
                ->title('Please answer all questions before submitting.')
                ->danger()
                ->send();
            return false;
        }

        return true;
    }

    /**
     * Update existing evaluation record
     */
    protected function updateExistingEvaluation(array $data): void
    {
        $this->evaluation->update(['answers' => $data]);
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
            'answers' => $data,
        ]);
    }

    /**
     * Send success notification to user
     */
    protected function sendSuccessNotification(): void
    {
        Notification::make()
            ->title('Evaluation saved successfully!')
            ->success()
            ->send();
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
        return [
            Action::make('save')
                ->label('Save Evaluation')
                ->action('save')
                ->keyBindings(['mod+s'])
                ->color('success'),
        ];
    }
}
