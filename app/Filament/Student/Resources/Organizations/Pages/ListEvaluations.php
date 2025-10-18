<?php

namespace App\Filament\Student\Resources\Organizations\Pages;

use App\Models\Student;
use App\Models\Evaluation;
use App\Models\OrganizationPeerEvaluator;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ListEvaluations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = \App\Filament\Student\Resources\Organizations\OrganizationResource::class;

    protected string $view = 'filament.student.resources.organizations.pages.list-evaluations';

    protected ?string $heading = 'My Evaluations';
    
    protected ?string $subheading = 'Complete your self-evaluations and assigned peer evaluations';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Split::make([
                    ImageColumn::make('organization_logo')
                        ->circular()
                        ->size(50)
                        ->getStateUsing(function ($record) {
                            return 'https://ui-avatars.com/api/?name=' . urlencode($record->organization_name) . '&color=7F9CF5&background=EBF4FF';
                        })
                        ->grow(false),
                    
                    Stack::make([
                        TextColumn::make('evaluation_title')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->color('primary')
                            ->getStateUsing(function ($record) {
                                if ($record->type === 'self') {
                                    return 'Self-Evaluation';
                                } else {
                                    return 'Peer Evaluation for ' . $record->target_student_name;
                                }
                            }),
                        
                        TextColumn::make('organization_name')
                            ->size('sm')
                            ->color('gray')
                            ->icon('heroicon-o-building-office'),
                        
                        TextColumn::make('department_name')
                            ->size('sm')
                            ->color('gray')
                            ->icon('heroicon-o-building-library'),
                    ])->space(2),
                    
                    Stack::make([
                        TextColumn::make('evaluation_type')
                            ->label('Type')
                            ->badge()
                            ->getStateUsing(function ($record) {
                                return $record->type === 'self' ? 'Self Evaluation' : 'Peer Evaluation';
                            })
                            ->color(fn ($record): string => $record->type === 'self' ? 'primary' : 'info')
                            ->icon(fn ($record): string => $record->type === 'self' ? 'heroicon-o-user' : 'heroicon-o-user-group'),
                        
                        TextColumn::make('status')
                            ->badge()
                            ->getStateUsing(fn ($record) => $record->is_completed ? 'Completed' : 'Pending')
                            ->color(fn ($record): string => $record->is_completed ? 'success' : 'warning')
                            ->icon(fn ($record): string => $record->is_completed ? 'heroicon-o-check-circle' : 'heroicon-o-clock'),
                        
                        TextColumn::make('due_date')
                            ->label('Due Date')
                            ->getStateUsing(function ($record) {
                                return 'No due date';
                            })
                            ->size('sm')
                            ->color('gray'),
                    ])->space(1)->alignEnd(),
                ]),
            ])
            ->actions([
                Action::make('evaluate')
                    ->label(fn ($record) => $record->is_completed ? 'Review' : 'Evaluate')
                    ->icon(fn ($record) => $record->is_completed ? 'heroicon-o-eye' : 'heroicon-o-pencil-square')
                    ->color(fn ($record) => $record->is_completed ? 'gray' : 'primary')
                    ->button()
                    ->size('sm')
                    ->url(fn ($record) => $this->getEvaluationUrl($record))
                    ->tooltip(fn ($record) => $record->is_completed ? 'Review your evaluation' : 'Complete this evaluation'),
            ])
            ->emptyStateHeading('No Evaluations Found')
            ->emptyStateDescription('You have no pending or completed evaluations at this time.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->striped();
    }

    protected function getTableQuery(): Builder
    {
        // For Filament table compatibility, we need to return a proper query builder
        // We'll override the pagination method instead
        return Student::query()->whereRaw('1 = 0'); // Empty query as placeholder
    }

    protected function paginateTableQuery($query): \Illuminate\Contracts\Pagination\Paginator
    {
        $tasks = $this->getEvaluationTasks();
        
        $page = request('page', 1);
        $perPage = 10;
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $tasks->forPage($page, $perPage)->values(),
            $tasks->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    /**
     * Get evaluation tasks as individual records for the current student
     */
    protected function getEvaluationTasks()
    {
        $studentId = auth('student')->id();
        if (!$studentId) {
            return collect([]);
        }

        $tasks = collect([]);

        // Get student's organizations for self-evaluations
        $student = Student::with(['organizations.department'])->find($studentId);
        
        foreach ($student->organizations as $organization) {
            // Add self-evaluation task
            $selfEvaluation = $this->getSelfEvaluation($organization);
            $tasks->push((object)[
                'id' => 'self_' . $organization->id,
                'type' => 'self',
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'department_name' => $organization->department->name ?? 'No Department',
                'target_student_id' => $studentId,
                'target_student_name' => $student->name,
                'is_completed' => $selfEvaluation !== null,
                'evaluation' => $selfEvaluation,
                'assignment' => null,
            ]);

            // Add peer evaluation tasks
            $peerAssignments = $this->getPeerAssignments($organization);
            foreach ($peerAssignments as $assignment) {
                $targetStudent = $assignment->evaluateeStudent;
                $peerEvaluation = $this->getPeerEvaluationForStudent($organization, $targetStudent->id);
                
                $tasks->push((object)[
                    'id' => 'peer_' . $organization->id . '_' . $targetStudent->id,
                    'type' => 'peer',
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'department_name' => $organization->department->name ?? 'No Department',
                    'target_student_id' => $targetStudent->id,
                    'target_student_name' => $targetStudent->name,
                    'is_completed' => $peerEvaluation !== null,
                    'evaluation' => $peerEvaluation,
                    'assignment' => $assignment,
                ]);
            }
        }

        return $tasks;
    }

    /**
     * Get self-evaluation for current student and organization
     */
    protected function getSelfEvaluation($organization): ?Evaluation
    {
        $studentId = auth('student')->id();
        if (!$studentId || !$organization || !isset($organization->id)) {
            return null;
        }
        
        return Evaluation::where([
            'organization_id' => $organization->id,
            'student_id' => $studentId,
            'evaluator_type' => 'self'
        ])->whereNull('evaluator_id')->first();
    }
    
    /**
     * Get peer evaluation assignments for current student
     */
    protected function getPeerAssignments($organization)
    {
        $studentId = auth('student')->id();
        if (!$studentId || !$organization || !isset($organization->id)) {
            return collect([]);
        }
        
        return OrganizationPeerEvaluator::where('organization_id', $organization->id)
            ->where('evaluator_student_id', $studentId)
            ->with(['evaluateeStudent', 'assignedByUser'])
            ->get();
    }
    
    /**
     * Get specific peer evaluation for a target student
     */
    protected function getPeerEvaluationForStudent($organization, $targetStudentId): ?Evaluation
    {
        $studentId = auth('student')->id();
        if (!$studentId || !$organization || !isset($organization->id)) {
            return null;
        }
        
        return Evaluation::where([
            'organization_id' => $organization->id,
            'student_id' => $targetStudentId,
            'evaluator_type' => 'peer',
            'evaluator_id' => $studentId
        ])->first();
    }
    
    /**
     * Generate evaluation URL based on task data
     */
    protected function getEvaluationUrl($record): string
    {
        if ($record->type === 'self') {
            return route('filament.student.resources.organizations.self-evaluate', [
                'organization' => $record->organization_id
            ]);
        } else {
            return route('filament.admin.resources.organizations.evaluate-student', [
                'organization' => $record->organization_id,
                'student' => $record->target_student_id,
                'type' => 'peer'
            ]);
        }
    }
}