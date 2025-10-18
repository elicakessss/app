<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationPeerEvaluator extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'evaluatee_student_id',
        'evaluator_student_id',
        'assigned_by_user_id',
        'assignment_notes',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
    public static function canEvaluateAsPeer(int $organizationId, int $evaluatorStudentId, int $evaluateeStudentId): bool
    {
        // Students cannot evaluate themselves
        if ($evaluatorStudentId === $evaluateeStudentId) {
            return false;
        }

        // Check if this peer assignment exists
        return static::where('organization_id', $organizationId)
            ->where('evaluator_student_id', $evaluatorStudentId)
            ->where('evaluatee_student_id', $evaluateeStudentId)
            ->exists();
    }

    /**
     * Get all students that a specific student can evaluate as peer in an organization
     */
    public static function getEvaluatableStudents(int $organizationId, int $evaluatorStudentId): array
    {
        return static::where('organization_id', $organizationId)
            ->where('evaluator_student_id', $evaluatorStudentId)
            ->with('evaluateeStudent')
            ->get()
            ->pluck('evaluateeStudent.id')
            ->toArray();
    }

    /**
     * Get all peer evaluators assigned to evaluate a specific student
     */
    public static function getAssignedPeerEvaluators(int $organizationId, int $evaluateeStudentId): array
    {
        return static::where('organization_id', $organizationId)
            ->where('evaluatee_student_id', $evaluateeStudentId)
            ->with('evaluatorStudent')
            ->get()
            ->pluck('evaluatorStudent.id')
            ->toArray();
    }
}