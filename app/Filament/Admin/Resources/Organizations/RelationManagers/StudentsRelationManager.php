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

/**
 * Students Relation Manager
 * 
 * Manages student memberships within organizations and provides evaluation actions.
 */
class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';
    protected static ?string $recordTitleAttribute = 'name';

    // ========================================
    // FORM CONFIGURATION
    // ========================================

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

    // ========================================
    // TABLE CONFIGURATION
    // ========================================

    public function table(Table $table): Table
    {
        return $table
            ->columns($this->getTableColumns())
            ->headerActions($this->getHeaderActions())
            ->actions($this->getTableActions())
            ->filters([])
            ->bulkActions([]);
    }

    /**
     * Define table columns
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Student Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('email')
                ->label('Email')
                ->searchable()
                ->toggleable(),

            TextColumn::make('pivot.position')
                ->label('Position')
                ->placeholder('No position assigned'),

            TextColumn::make('evaluation_status')
                ->label('Evaluation Status')
                ->badge()
                ->getStateUsing(function ($record) {
                    $adviser = $this->getEvaluationStatus($record->id, 'adviser');
                    $peer = $this->getEvaluationStatus($record->id, 'peer');
                    $self = $this->getEvaluationStatus($record->id, 'self');
                    
                    $completed = array_filter([$adviser, $peer, $self]);
                    $total = 3;
                    
                    if (count($completed) === $total) {
                        return 'Complete';
                    } elseif (count($completed) > 0) {
                        return count($completed) . '/' . $total . ' Done';
                    } else {
                        return 'Pending';
                    }
                })
                ->color(fn (string $state): string => match ($state) {
                    'Complete' => 'success',
                    'Pending' => 'danger',
                    default => 'warning',
                }),

            TextColumn::make('pivot.created_at')
                ->label('Added')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * Define header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            AttachAction::make()
                ->label('Add Student')
                ->form($this->getAttachForm())
                ->preloadRecordSelect(),
        ];
    }

    /**
     * Define row actions
     */
    protected function getTableActions(): array
    {
        return [
            $this->getDirectEvaluationAction(),
            $this->getOtherEvaluationsAction(),
            $this->getEditAction(),
            $this->getDetachAction(),
        ];
    }

    // ========================================
    // ACTION DEFINITIONS
    // ========================================

    /**
     * Create direct evaluation action (defaults to adviser)
     */
    protected function getDirectEvaluationAction(): Action
    {
        return Action::make('evaluate')
            ->label('Evaluate')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('primary')
            ->button()
            ->size('sm')
            ->url(fn ($record) => $this->getEvaluationUrl($record->id, 'adviser'))
            ->tooltip('Complete adviser evaluation for this student');
    }

    /**
     * Create additional evaluation types action group
     */
    protected function getOtherEvaluationsAction(): ActionGroup
    {
        return ActionGroup::make([
            $this->createEvaluationAction('peer', '👥 Peer', 'info'),
            $this->createEvaluationAction('self', '👤 Self', 'warning'),
            $this->createPeerAssignmentAction(),
        ])
        ->label('Other')
        ->icon('heroicon-o-ellipsis-horizontal')
        ->size('sm')
        ->color('gray');
    }

    /**
     * Create individual evaluation action
     */
    protected function createEvaluationAction(string $type, string $label, string $color): Action
    {
        return Action::make("{$type}_evaluation")
            ->label($label)
            ->color($color)
            ->url(fn ($record) => $this->getEvaluationUrl($record->id, $type));
    }

    /**
     * Create peer assignment action for assigning peer evaluators
     */
    protected function createPeerAssignmentAction(): Action
    {
        return Action::make('assign_peer_evaluators')
            ->label('⚙️ Assign Peer Evaluators')
            ->color('success')
            ->icon('heroicon-o-user-group')
            ->form([
                Select::make('peer_evaluators')
                    ->label('Select Peer Evaluators')
                    ->helperText('Choose up to 2 students who will evaluate this student as peers. Typically, advisers assign students who work closely with the evaluatee.')
                    ->multiple()
                    ->maxItems(2)
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
                    ->required(),
                
                Textarea::make('assignment_notes')
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
            ->modalDescription('In real-world evaluation processes, advisers typically assign 2 students as peer evaluators. These should be students who work closely with the evaluatee and can provide meaningful peer feedback.')
            ->modalWidth('lg');
    }

    /**
     * Generate evaluation URL for specific student and type
     */
    protected function getEvaluationUrl(int $studentId, string $evaluatorType): string
    {
        return OrganizationResource::getUrl('evaluate-student', [
            'organization' => $this->ownerRecord->id,
            'student' => $studentId,
            'type' => $evaluatorType,
        ]);
    }

    /**
     * Create edit action for student position
     */
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

    /**
     * Create detach action to remove student
     */
    protected function getDetachAction(): DetachAction
    {
        return DetachAction::make()->label('Remove');
    }

    // ========================================
    // FORM HELPERS
    // ========================================

    /**
     * Get form fields for attaching students
     */
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
                ->maxLength(255)
                ->placeholder('e.g., Team Leader, Secretary, Member'),
        ];
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    /**
     * Check evaluation completion status for a student
     */
    protected function getEvaluationStatus(int $studentId, string $evaluatorType): bool
    {
        $evaluation = Evaluation::where('organization_id', $this->ownerRecord->id)
            ->where('student_id', $studentId)
            ->where('evaluator_type', $evaluatorType)
            ->first();

        return $evaluation !== null;
    }

    /**
     * Assign peer evaluators to a student
     */
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
