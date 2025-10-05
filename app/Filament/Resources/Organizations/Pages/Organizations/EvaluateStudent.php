<?php

namespace App\Filament\Resources\Organizations\Pages\Organizations;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\Evaluation;
use App\Models\Organization;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class EvaluateStudent extends Page
{
    protected static string $resource = OrganizationResource::class;

    protected string $view = 'filament.resources.organizations.pages.organizations.evaluate-student';

    public Organization $organization;
    public Student $student;
    public string $type;
    public ?Evaluation $evaluation = null;
    public array $data = [];

    public function mount(): void
    {
        $this->organization = Organization::findOrFail(request()->route('organization'));
        $this->student = Student::findOrFail(request()->route('student'));
        $this->type = request()->route('type');

        // Check if evaluation already exists
        $this->evaluation = Evaluation::where('organization_id', $this->organization->id)
            ->where('student_id', $this->student->id)
            ->where('evaluator_type', $this->type)
            ->first();

        if ($this->evaluation) {
            $this->data = $this->evaluation->answers ?? [];
        }

        $this->form->fill($this->data);
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

    public function form(Form $form): Form
    {
        $questions = Evaluation::getQuestionsForEvaluator($this->type);
        $components = [];

        // Group questions by domain and strand
        $grouped = $this->groupQuestions($questions);

        foreach ($grouped as $domainName => $strands) {
            $domainComponents = [];

            foreach ($strands as $strandName => $strandQuestions) {
                $strandComponents = [];

                foreach ($strandQuestions as $questionKey => $questionText) {
                    $strandComponents[] = Radio::make($questionKey)
                        ->label($questionText)
                        ->options([
                            3 => 'Excellent (3)',
                            2 => 'Very Good (2)',
                            1 => 'Good (1)',
                            0 => 'Needs Improvement (0)',
                        ])
                        ->required()
                        ->inline()
                        ->columnSpanFull();
                }

                if (!empty($strandComponents)) {
                    $domainComponents[] = Section::make($strandName)
                        ->schema($strandComponents)
                        ->collapsible()
                        ->collapsed(false);
                }
            }

            if (!empty($domainComponents)) {
                $components[] = Section::make($domainName)
                    ->schema($domainComponents)
                    ->collapsible()
                    ->collapsed(false);
            }
        }

        return $form
            ->schema($components)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->evaluation) {
            $this->evaluation->update(['answers' => $data]);
        } else {
            Evaluation::create([
                'organization_id' => $this->organization->id,
                'student_id' => $this->student->id,
                'evaluator_type' => $this->type,
                'answers' => $data,
            ]);
        }

        Notification::make()
            ->title('Evaluation saved successfully!')
            ->success()
            ->send();

        $this->redirect(route('filament.admin.resources.organizations.view', ['record' => $this->organization->id]));
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

    private function groupQuestions(array $questions): array
    {
        $grouped = [];

        foreach ($questions as $key => $text) {
            if ($key === 'length_of_service') {
                $grouped['Length of Service']['Service Duration'][$key] = $text;
                continue;
            }

            // Parse question key (e.g., 'domain_1_strand_1_q1')
            $parts = explode('_', $key);

            if (count($parts) >= 4) {
                $domainNum = $parts[1];
                $strandNum = $parts[3];

                $domainName = match($domainNum) {
                    '1' => 'Domain 1: Paulinian Leadership as Social Responsibility',
                    '2' => 'Domain 2: Paulinian Leadership as a Life of Service',
                    '3' => 'Domain 3: Paulinian Leader as Leading by Example',
                    default => "Domain {$domainNum}"
                };

                $strandName = "Strand {$strandNum}";

                $grouped[$domainName][$strandName][$key] = $text;
            }
        }

        return $grouped;
    }
}
