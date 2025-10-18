<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Evaluation Model
 * 
 * Stores evaluation scores for students by different evaluator types.

 * Automatically calculates evaluator scores and triggers rank recalculation.
 */
class Evaluation extends Model
{
    use HasFactory;

    /**
     * Public method to get peer questions for students
     */
    public static function getPeerQuestionsForStudents(): array
    {
        $allQuestions = self::getAllQuestions();
        // For now, use the same rubric/questions as self-evaluation. Replace with peer-specific questions if needed.
        return self::getSelfQuestions($allQuestions);
    }
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'student_id',
        'evaluator_type',
        'evaluator_id', // Add evaluator_id to fillable
        'answers',
        'evaluator_score',
    ];

    protected $casts = [
        'answers' => 'array',
        'evaluator_score' => 'decimal:3',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the student who performed this evaluation (for peer evaluations)
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'evaluator_id');
    }

    // ========================================
    // QUESTION MANAGEMENT
    // ========================================

    /**
     * Get questions visible to specific evaluator type
     */
    public static function getQuestionsForEvaluator(string $evaluatorType): array
    {
        $allQuestions = self::getAllQuestions();
        
        return match ($evaluatorType) {
            'adviser' => $allQuestions, // All domains + length of service
            'peer' => self::getPeerQuestions($allQuestions),
            'self' => self::getSelfQuestions($allQuestions),
            default => [],
        };
    }

    /**
     * Get peer evaluator questions (Domain 2 all strands + Domain 3 strands 1-2)
     */
    protected static function getPeerQuestions(array $allQuestions): array
    {
        return [
            // Domain 2: All strands (1-3)
            'domain_2_strand_1_q1' => $allQuestions['domain_2_strand_1_q1'],
            'domain_2_strand_2_q1' => $allQuestions['domain_2_strand_2_q1'],
            'domain_2_strand_2_q2' => $allQuestions['domain_2_strand_2_q2'],
            'domain_2_strand_3_q1' => $allQuestions['domain_2_strand_3_q1'],
            'domain_2_strand_3_q2' => $allQuestions['domain_2_strand_3_q2'],
            // Domain 3: Strands 1-2
            'domain_3_strand_1_q1' => $allQuestions['domain_3_strand_1_q1'],
            'domain_3_strand_2_q1' => $allQuestions['domain_3_strand_2_q1'],
        ];
    }

    /**
     * Get self evaluator questions (Domain 2 strands 1-2 + Domain 3 all strands)
     */
    protected static function getSelfQuestions(array $allQuestions): array
    {
        return [
            // Domain 2: Strands 1-2
            'domain_2_strand_1_q1' => $allQuestions['domain_2_strand_1_q1'],
            'domain_2_strand_2_q1' => $allQuestions['domain_2_strand_2_q1'],
            'domain_2_strand_2_q2' => $allQuestions['domain_2_strand_2_q2'],
            // Domain 3: All strands
            'domain_3_strand_1_q1' => $allQuestions['domain_3_strand_1_q1'],
            'domain_3_strand_2_q1' => $allQuestions['domain_3_strand_2_q1'],
        ];
    }

    /**
     * Public method to get self questions for students
     */
    public static function getSelfQuestionsForStudents(): array
    {
        $allQuestions = self::getAllQuestions();
        return self::getSelfQuestions($allQuestions);
    }

    /**
     * Get complete rubric questions with proper structure
     */
    public static function getAllQuestions(): array
    {
        return [
            // Domain 1: Paulinian Leadership as Social Responsibility
            'domain_1_strand_1_q1' => 'The Paulinian Leader organizes/co-organizes and/or serves as resource speaker in seminars and activities for the organization.',
            'domain_1_strand_1_q2' => 'The Paulinian Leader facilitates/co-facilitates seminars and activities for the organization.',
            'domain_1_strand_1_q3' => 'The Paulinian Leader participates in seminars/activities of the organization.',
            'domain_1_strand_1_q4' => 'The Paulinian Leader attends SPUP-organized seminars and activities related to the organization.',
            'domain_1_strand_2_q1' => 'The Paulinian Leader ensures quality in all tasks/assignments given.',
            
            // Domain 2: Paulinian Leadership as a Life of Service
            'domain_2_strand_1_q1' => 'The Paulinian Leader performs related tasks outside the given assignment: initiates actions to solve issues among students and those that concern the organization/university; and participates in the aftercare during activities.',
            'domain_2_strand_2_q1' => 'The Paulinian Leader shares in the organization\'s management and evaluation of the organization.',
            'domain_2_strand_2_q2' => 'The Paulinian Leader shares in the organization: management and evaluation of projects/activities of the university.',
            'domain_2_strand_3_q1' => 'The Paulinian Leader attends regular meetings.',
            'domain_2_strand_3_q2' => 'The Paulinian Leader attends all emergency meetings called.',
            
            // Domain 3: Paulinian Leader as Leading by Example (Discipline/Decorum)
            'domain_3_strand_1_q1' => 'The Paulinian Leader is a model of grooming and proper decorum.',
            'domain_3_strand_2_q1' => 'The Paulinian Leader ensures cleanliness and orderliness of office/workplace.',
            
            // Length of Service
            'length_of_service' => 'Paulinian Leader had served the Department/University',
        ];
    }

    // ========================================
    // SCORE CALCULATION
    // ========================================

    /**
     * Calculate average score from answers
     */
    public function calculateScore(): float
    {
        if (!$this->answers || empty($this->answers)) {
            return 0;
        }

        $scores = array_filter($this->answers, 'is_numeric');
        
        return count($scores) > 0 
            ? round(array_sum($scores) / count($scores), 3) 
            : 0;
    }

    // ========================================
    // MODEL EVENTS
    // ========================================

    protected static function booted(): void
    {
        static::saving(function (Evaluation $evaluation) {
            $evaluation->evaluator_score = $evaluation->calculateScore();
        });

        static::saved(function (Evaluation $evaluation) {
            // Trigger rank recalculation
            Rank::updateForStudent($evaluation->organization_id, $evaluation->student_id);
        });
    }
}
