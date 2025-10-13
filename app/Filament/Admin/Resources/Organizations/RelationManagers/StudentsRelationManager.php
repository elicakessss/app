<?php

namespace App\Filament\Admin\Resources\Organizations\RelationManagers;

use App\Models\Student;
use App\Models\Evaluation;
use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
            $this->getEvaluationActionGroup(),
            $this->getEditAction(),
            $this->getDetachAction(),
        ];
    }

    // ========================================
    // ACTION DEFINITIONS
    // ========================================

    /**
     * Create evaluation action group with all evaluator types
     */
    protected function getEvaluationActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            $this->createEvaluationAction('adviser', '🎓 Adviser', 'success'),
            $this->createEvaluationAction('peer', '👥 Peer', 'info'),
            $this->createEvaluationAction('self', '👤 Self', 'warning'),
        ])
        ->label('Evaluate')
        ->button();
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
}
