<?php

namespace App\Filament\Admin\Resources\Organizations\RelationManagers;

use App\Models\Student;
use App\Models\Evaluation;
use App\Models\OrganizationPeerEvaluator;
use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

// Students Relation Manager: Manages student memberships and evaluations within organizations.
class StudentsRelationManager extends RelationManager
{

        protected static string $relationship = 'students';
        protected static ?string $recordTitleAttribute = 'name';

        /**
         * Get the evaluation score for a student and type (self, peer, adviser)
         */
        protected function getEvaluationScore(int $studentId, string $evaluatorType): string
        {
            $evaluation = Evaluation::where('organization_id', $this->ownerRecord->id)
                ->where('student_id', $studentId)
                ->where('evaluator_type', $evaluatorType)
                ->first();

            if ($evaluation && $evaluation->evaluator_score !== null) {
                return number_format($evaluation->evaluator_score, 2);
            }
            return '-';
        }



    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., Team Leader, Secretary, Member'),
        ]);
    }



    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->getTableActions())
            ->filters([])
            ->bulkActions([])
            ->striped();
    }

    // Table columns configuration
    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\ColumnGroup::make('Student', [
                \Filament\Tables\Columns\ImageColumn::make('profile_picture')
                    ->label('Profile')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('pivot.position')
                    ->label('Position')
                    ->placeholder('No position assigned'),
            ]),
            \Filament\Tables\Columns\ColumnGroup::make('Evaluation', [
                \Filament\Tables\Columns\TextColumn::make('self_score')
                    ->label('Self')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'self')),
                \Filament\Tables\Columns\TextColumn::make('peer_score')
                    ->label('Peer')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'peer')),
                \Filament\Tables\Columns\TextColumn::make('adviser_score')
                    ->label('Adviser')
                    ->getStateUsing(fn ($record) => $this->getEvaluationScore($record->id, 'adviser')),
            ]),
        ];
    }

    // Header actions configuration
    protected function getHeaderActions(): array
    {
        return [
            AttachAction::make()
                ->label('Add Student')
                ->form($this->getAttachForm())
                ->preloadRecordSelect(),
            $this->getBulkPeerAssignmentAction(),
        ];
    }

    // Bulk peer assignment action for header
    protected function getBulkPeerAssignmentAction(): Action
    {
        return Action::make('assign_peer_evaluators')
            ->label('Assign Peer Evaluators')
            ->color('success')
            ->icon('heroicon-o-user-group')
            ->modalHeading('Assign Peer Evaluators')
            ->modalDescription('Assign up to 2 peer evaluators for each student. Assigned peers are checked and cannot be changed.')
            ->modalWidth('xl')
            ->form(function () {
                $students = $this->ownerRecord->students()->get();
                $fields = [];
                foreach ($students as $student) {
                    $studentId = $student->id;
                    $assigned = OrganizationPeerEvaluator::where('organization_id', $this->ownerRecord->id)
                        ->where('evaluatee_student_id', $studentId)
                        ->pluck('evaluator_student_id')
                        ->toArray();
                    $options = $this->ownerRecord->students()
                        ->where('students.id', '!=', $studentId)
                        ->pluck('name', 'students.id')
                        ->toArray();
                    $fields[] = \Filament\Forms\Components\CheckboxList::make('peer_evaluators_' . $studentId)
                        ->label($student->name)
                        ->options($options)
                        ->default($assigned)
                        ->disabled($assigned)
                        ->maxItems(2)
                        ->columns(2)
                        ->helperText('Select up to 2 peer evaluators for ' . $student->name);
                }
                return $fields;
            })
            ->action(function ($data) {
                $students = $this->ownerRecord->students()->get();
                foreach ($students as $student) {
                    $studentId = $student->id;
                    $key = 'peer_evaluators_' . $studentId;
                    $peerEvaluatorIds = $data[$key] ?? [];
                    $this->assignPeerEvaluators($studentId, $peerEvaluatorIds, null);
                }
            });
    }

    // Row actions configuration
    protected function getTableActions(): array
    {
        return [
            $this->getDirectEvaluationAction(),
            $this->getOtherEvaluationsAction(),
            $this->getEditAction(),
            $this->getDetachAction(),
        ];
    }



    // Direct evaluation action (defaults to adviser)
    protected function getDirectEvaluationAction(): Action
    {
        return Action::make('evaluate')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('primary')
            ->button()
            ->size('sm')
            ->url(fn ($record) => $this->getEvaluationUrl($record->id, 'adviser'))
            ->tooltip('Complete adviser evaluation for this student');
    }

    // Additional evaluation types action group
    protected function getOtherEvaluationsAction(): ActionGroup
    {
        return ActionGroup::make([
            $this->createEvaluationAction('peer', '👥 Peer', 'info'),
            $this->createEvaluationAction('self', '👤 Self', 'warning'),
            $this->createPeerAssignmentAction(),
        ])
        ->icon('heroicon-o-ellipsis-horizontal')
        ->button()
        ->color('primary')
        ->size('sm');
    }

    // Individual evaluation action
    protected function createEvaluationAction(string $type, string $label, string $color): Action
    {
        return Action::make("{$type}_evaluation")
            ->label($label)
            ->color($color)
            ->url(fn ($record) => $this->getEvaluationUrl($record->id, $type));
    }

    // Peer assignment action for assigning peer evaluators
    protected function createPeerAssignmentAction(): Action
    {
        return Action::make('assign_peer_evaluators')
            ->label('⚙️ Assign Peer Evaluators')
            ->color('success')
            ->icon('heroicon-o-user-group')
            ->form([
                \Filament\Forms\Components\CheckboxList::make('peer_evaluators')
                    ->label('Select Peer Evaluators')
                    ->helperText('Choose up to 2 students who will evaluate this student as peers. Assigned peers are checked and cannot be changed.')
                    ->options(function ($record) {
                        // Get all students in the organization except the current student
                        return $this->ownerRecord->students()
                            ->where('students.id', '!=', $record->id)
                            ->pluck('name', 'students.id')
                            ->toArray();
                    })
                    ->default(function ($record) {
                        // Load existing peer evaluator assignments
                        return OrganizationPeerEvaluator::where('organization_id', $this->ownerRecord->id)
                            ->where('evaluatee_student_id', $record->id)
                            ->pluck('evaluator_student_id')
                            ->toArray();
                    })
                    ->disabled(function ($record) {
                        // Disable already assigned peer evaluators
                        $assigned = OrganizationPeerEvaluator::where('organization_id', $this->ownerRecord->id)
                            ->where('evaluatee_student_id', $record->id)
                            ->pluck('evaluator_student_id')
                            ->toArray();
                        $options = $this->ownerRecord->students()
                            ->where('students.id', '!=', $record->id)
                            ->pluck('students.id')
                            ->toArray();
                        $disabled = [];
                        foreach ($options as $id) {
                            if (in_array($id, $assigned)) {
                                $disabled[] = $id;
                            }
                        }
                        return $disabled;
                    })
                    ->columns(2)
                    ->required(),
                \Filament\Forms\Components\Textarea::make('assignment_notes')
                    ->label('Assignment Notes')
                    ->helperText('Optional notes about why these students were chosen as peer evaluators')
                    ->placeholder('e.g., "John and Mary work closely with the student on daily tasks and projects"')
                    ->rows(3)
                    ->maxLength(500),
            ])
            ->action(function ($record, $data) {
                $this->assignPeerEvaluators($record->id, $data['peer_evaluators'], $data['assignment_notes'] ?? null);
            })
            ->modalHeading(fn ($record) => 'Assign Peer Evaluators for ' . $record->name)
            ->modalDescription('Assigned peer evaluators are checked and cannot be changed. Advisers may assign up to 2 students as peer evaluators.')
            ->modalWidth('lg');
    }

    // Generate evaluation URL for specific student and type
    protected function getEvaluationUrl(int $studentId, string $evaluatorType): string
    {
        return OrganizationResource::getUrl('evaluate-student', [
            'organization' => $this->ownerRecord->id,
            'student' => $studentId,
            'type' => $evaluatorType,
        ]);
    }

    // Edit action for student position
    protected function getEditAction(): EditAction
    {
        return EditAction::make()
            ->form([
                TextInput::make('position')
                    ->label('Position')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    // Detach action to remove student
    protected function getDetachAction(): DetachAction
    {
        return DetachAction::make()->label('Remove');
    }



    // Form fields for attaching students
    protected function getAttachForm(): array
    {
        return [
            Select::make('recordId')
                ->label('Student')
                ->options(Student::all()->pluck('name', 'id'))
                ->searchable()
                ->required(),
            
            TextInput::make('position')
                ->label('Position')
                ->required()
                ->maxLength(255),
        ];
    }



    // Check evaluation completion status for a student
    protected function getEvaluationStatus(int $studentId, string $evaluatorType): bool
    {
        $evaluation = Evaluation::where('organization_id', $this->ownerRecord->id)
            ->where('student_id', $studentId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        return $evaluation !== null;
    }

    // Assign peer evaluators to a student
    protected function assignPeerEvaluators(int $evaluateeStudentId, array $peerEvaluatorIds, ?string $notes): void
    {
        try {
            // Remove existing peer evaluator assignments for this student
            OrganizationPeerEvaluator::where('organization_id', $this->ownerRecord->id)
                ->where('evaluatee_student_id', $evaluateeStudentId)
                ->delete();

            // Create new peer evaluator assignments
            foreach ($peerEvaluatorIds as $evaluatorId) {
                OrganizationPeerEvaluator::create([
                    'organization_id' => $this->ownerRecord->id,
                    'evaluatee_student_id' => $evaluateeStudentId,
                    'evaluator_student_id' => $evaluatorId,
                    'assigned_by_user_id' => auth()->id(),
                    'assignment_notes' => $notes,
                    'assigned_at' => now(),
                ]);
            }

            $evaluateeName = Student::find($evaluateeStudentId)->name;
            $evaluatorNames = Student::whereIn('id', $peerEvaluatorIds)->pluck('name')->join(', ');

            Notification::make()
                ->title('Peer Evaluators Assigned Successfully')
                ->body("Assigned {$evaluatorNames} as peer evaluators for {$evaluateeName}")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error Assigning Peer Evaluators')
                ->body('There was an error assigning the peer evaluators. Please try again.')
                ->danger()
                ->send();
        }
    }
}
