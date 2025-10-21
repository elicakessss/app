<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationPeerEvaluator extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'evaluatee_student_id',
        'evaluator_student_id',
        'assigned_by_user_id',
        'assignment_notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function evaluateeStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'evaluatee_student_id');
    }

    public function evaluatorStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'evaluator_student_id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Check if a student can evaluate another student as peer
     */
    public static function canEvaluateAsPeer(int $evaluationId, int $evaluatorStudentId, int $evaluateeStudentId): bool
    {
        // Students cannot evaluate themselves
        if ($evaluatorStudentId === $evaluateeStudentId) {
            return false;
        }

        // Check if this peer assignment exists
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_student_id', $evaluatorStudentId)
            ->where('evaluatee_student_id', $evaluateeStudentId)
            ->exists();
    }

    /**
     * Get all students that a specific student can evaluate as peer in an evaluation
     */
    public static function getEvaluatableStudents(int $evaluationId, int $evaluatorStudentId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluator_student_id', $evaluatorStudentId)
            ->with('evaluateeStudent')
            ->get()
            ->pluck('evaluateeStudent.id')
            ->toArray();
    }

    /**
     * Get all peer evaluators assigned to evaluate a specific student
     */
    public static function getAssignedPeerEvaluators(int $evaluationId, int $evaluateeStudentId): array
    {
        return static::where('evaluation_id', $evaluationId)
            ->where('evaluatee_student_id', $evaluateeStudentId)
            ->with('evaluatorStudent')
            ->get()
            ->pluck('evaluatorStudent.id')
            ->toArray();
    }
}