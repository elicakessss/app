<?php

namespace App\Filament\Student\Resources\Organizations\Pages;

use App\Filament\Student\Resources\Organizations\OrganizationResource;
use App\Models\Student;
use App\Models\Evaluation;
use App\Models\OrganizationPeerEvaluator;
use Filament\Resources\Pages\Page;

// Student evaluation tasks page (custom Blade view)
class ListOrganizations extends Page
{
    protected static string $resource = OrganizationResource::class;

    protected ?string $heading = 'My Evaluations';
    
    protected ?string $subheading = 'Complete your self-evaluations and assigned peer evaluations';

    protected function getHeaderActions(): array
    {
        return [];
    }


    public function getView(): string
    {
    return 'filament.student.resources.organizations.pages.EvaluationList';
    }

    protected function getViewData(): array
    {
        return [
            'tasks' => $this->getEvaluationTasks(),
        ];
    }

    protected function getEvaluationTasks()
    {
        $studentId = auth('student')->id();
        if (!$studentId) {
            return collect([]);
        }

        $tasks = collect();
        $student = Student::with(['organizations.department'])->find($studentId);
        
        if (!$student) {
            return collect([]);
        }


        // Self-evaluation tasks
        foreach ($student->organizations as $organization) {
            $selfEvaluation = Evaluation::where([
                'organization_id' => $organization->id,
                'student_id' => $studentId,
                'evaluator_type' => 'self'
            ])->whereNull('evaluator_id')->first();

            $tasks->push([
                'id' => 'self_' . $organization->id,
                'task_type' => 'Self-Evaluation',
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'department_name' => $organization->department->name ?? 'No Department',
                'target_name' => 'Yourself',
                'status' => $selfEvaluation ? 'Completed' : 'Pending',
            ]);
        }

        // Peer evaluation tasks
        $peerAssignments = OrganizationPeerEvaluator::where('evaluator_student_id', $studentId)
            ->with(['organization.department', 'evaluateeStudent'])
            ->get();

        foreach ($peerAssignments as $assignment) {
            $peerEvaluation = Evaluation::where([
                'organization_id' => $assignment->organization_id,
                'student_id' => $assignment->evaluatee_student_id,
                'evaluator_type' => 'peer',
                'evaluator_id' => $studentId
            ])->first();

            $tasks->push([
                'id' => 'peer_' . $assignment->organization_id . '_' . $assignment->evaluatee_student_id,
                'task_type' => 'Peer Evaluation',
                'organization_id' => $assignment->organization_id,
                'organization_name' => $assignment->organization->name,
                'department_name' => $assignment->organization->department->name ?? 'No Department',
                'target_id' => $assignment->evaluatee_student_id,
                'target_name' => $assignment->evaluateeStudent->name,
                'status' => $peerEvaluation ? 'Completed' : 'Pending',
            ]);
        }

        return $tasks;
    }
}