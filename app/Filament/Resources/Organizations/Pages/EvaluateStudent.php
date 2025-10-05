<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Evaluate Student Page
 * 
 * Provides dynamic evaluation forms based on evaluator type (adviser, peer, self).
 * Organizes questions by domain and strand for better user experience.
 */
class EvaluateStudent extends Page
{
    protected static string $resource = OrganizationResource::class;
    protected string $view = 'filament.resources.organizations.pages.evaluate-student';

    // ========================================
    // PROPERTIES
    // ========================================

    public Organization $organization;
    public Student $student;
    public string $type;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    // ========================================
    // DOMAIN MAPPINGS
    // ========================================

    /** Domain number to title mapping */
    private const DOMAIN_TITLES = [
        '1' => 'Domain 1: Paulinian Leadership as Social Responsibility',
        '2' => 'Domain 2: Paulinian Leadership as a Life of Service',
        '3' => 'Domain 3: Paulinian Leader as Leading by Example',
    ];

    /** Score options for evaluation questions */
    private const SCORE_OPTIONS = [
        3 => 'Excellent (3)',
        2 => 'Very Good (2)',
        1 => 'Good (1)',
        0 => 'Needs Improvement (0)',
    ];

    // ========================================
    // LIFECYCLE METHODS
    // ========================================

    public function mount(): void
    {
        $this->loadRouteParameters();
        $this->loadExistingEvaluation();
        $this->initializeForm();
    }

    /**
     * Load organization, student, and evaluation type from route
     */
    protected function loadRouteParameters(): void
    {
        $this->organization = Organization::findOrFail(request()->route('organization'));
        $this->student = Student::findOrFail(request()->route('student'));
        $this->type = request()->route('type');
    }

    /**
     * Load existing evaluation if it exists
     */
    protected function loadExistingEvaluation(): void
    {
        $this->evaluation = Evaluation::where('organization_id', $this->organization->id)
            ->where('student_id', $this->student->id)
            ->where('evaluator_type', $this->type)
            ->first();

        if ($this->evaluation) {
            $this->data = $this->evaluation->answers ?? [];
        }
    }

    /**
     * Initialize form with existing data
     */
    protected function initializeForm(): void
    {
        $this->form->fill($this->data);
    }

    // ========================================
    // PAGE METADATA
    // ========================================

    public function getTitle(): string|Htmlable
    {
        $evaluatorTitle = ucfirst($this->type);
        return "{$evaluatorTitle} Evaluation - {$this->student->name}";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return "Organization: {$this->organization->name}";
    }

    // ========================================
    // FORM BUILDING
    // ========================================

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->buildFormSchema())
            ->statePath('data');
    }

    /**
     * Build the complete form schema with organized questions
     */
    protected function buildFormSchema(): array
    {
        $questions = Evaluation::getQuestionsForEvaluator($this->type);
        $grouped = $this->groupQuestions($questions);
        $components = [];

        foreach ($grouped as $domainName => $strands) {
            $domainComponents = $this->buildDomainComponents($strands);
            
            if (!empty($domainComponents)) {
                $components[] = Section::make($domainName)
                    ->schema($domainComponents)
                    ->collapsible()
                    ->collapsed(false);
            }
        }

        return $components;
    }

    /**
     * Build components for all strands within a domain
     */
    protected function buildDomainComponents(array $strands): array
    {
        $domainComponents = [];

        foreach ($strands as $strandName => $strandQuestions) {
            $strandComponents = $this->buildStrandComponents($strandQuestions);
            
            if (!empty($strandComponents)) {
                $domainComponents[] = Section::make($strandName)
                    ->schema($strandComponents)
                    ->collapsible()
                    ->collapsed(false);
            }
        }

        return $domainComponents;
    }

    /**
     * Build radio components for all questions within a strand
     */
    protected function buildStrandComponents(array $strandQuestions): array
    {
        $strandComponents = [];

        foreach ($strandQuestions as $questionKey => $questionText) {
            $strandComponents[] = Radio::make($questionKey)
                ->label($questionText)
                ->options(self::SCORE_OPTIONS)
                ->required()
                ->inline()
                ->columnSpanFull();
        }

        return $strandComponents;
    }

    // ========================================
    // QUESTION ORGANIZATION
    // ========================================

    /**
     * Group questions by domain and strand for organized display
     */
    protected function groupQuestions(array $questions): array
    {
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
    protected function categorizeQuestion(string $key, string $text, array &$grouped): void
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

    // ========================================
    // ACTIONS
    // ========================================

    /**
     * Save evaluation data to database
     */
    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->evaluation) {
            $this->updateExistingEvaluation($data);
        } else {
            $this->createNewEvaluation($data);
        }

        $this->sendSuccessNotification();
        $this->redirectToOrganization();
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

    // ========================================
    // HEADER ACTIONS
    // ========================================

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
