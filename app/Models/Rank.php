<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Rank Model
 * 
 * Stores final ranking calculations for students based on weighted evaluation scores.
 * Weights: Adviser 65%, Peer 25%, Self 10%
 */
class Rank extends Model
{
    use HasFactory;

    protected $table = 'ranks';

    protected $fillable = [
        'evaluation_id',
        'student_id',
        'final_score',
        'rank',
        'status',
        'breakdown',
    ];

    protected $casts = [
        'final_score' => 'decimal:3',
        'breakdown' => 'array',
    ];

    // ========================================
    // CONSTANTS
    // ========================================

    /** Score weighting for final calculation */
    public const WEIGHTS = [
        'adviser' => 0.65, // 50% + 15% (includes length of service)
        'peer' => 0.25,
        'self' => 0.10,
    ];

    /** Rank thresholds for tier determination */
    public const RANK_THRESHOLDS = [
        'gold' => 2.41,
        'silver' => 1.81,
        'bronze' => 1.21,
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ========================================
    // RANK CALCULATION
    // ========================================

    /**
     * Update or create final result for a student
     */
    public static function updateForStudent(int $evaluationId, int $studentId): void
    {
        $evaluationScores = EvaluationScore::where('evaluation_id', $evaluationId)
            ->where('student_id', $studentId)
            ->get()
            ->keyBy('evaluator_type');
        
        $rank = self::firstOrCreate([
            'evaluation_id' => $evaluationId,
            'student_id' => $studentId,
        ]);

        // Calculate weighted breakdown
        $breakdown = self::calculateBreakdown($evaluationScores);
        
        // Determine completion status
        $isFinalized = self::isFinalized($evaluationScores);
        
        // Calculate final score and rank
        [$finalScore, $rankTier, $status] = self::computeFinalRanking($breakdown, $isFinalized);

        $rank->update([
            'final_score' => $finalScore,
            'rank' => $rankTier,
            'status' => $status,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get evaluation scores for the student by evaluator type
     */
    protected static function getEvaluationScores(int $evaluationId, int $studentId): \Illuminate\Support\Collection
    {
        return EvaluationScore::where('evaluation_id', $evaluationId)
            ->where('student_id', $studentId)
            ->get()
            ->keyBy('evaluator_type');
    }

    /**
     * Calculate weighted score breakdown for each evaluator type
     */
    protected static function calculateBreakdown(\Illuminate\Support\Collection $evaluationScores): array
    {
        $breakdown = [];

        foreach (self::WEIGHTS as $evaluatorType => $weight) {
            if (isset($evaluationScores[$evaluatorType])) {
                $score = $evaluationScores[$evaluatorType]->evaluator_score;
                $breakdown[$evaluatorType] = [
                    'score' => $score,
                    'weight' => $weight,
                    'weighted_score' => $score * $weight,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Check if all required evaluation scores are present
     */
    protected static function isFinalized(\Illuminate\Support\Collection $evaluationScores): bool
    {
        return isset($evaluationScores['adviser']) && 
               isset($evaluationScores['peer']) && 
               isset($evaluationScores['self']);
    }

    /**
     * Compute final ranking based on breakdown
     */
    protected static function computeFinalRanking(array $breakdown, bool $isFinalized): array
    {
        if (!$isFinalized) {
            return [null, null, 'pending'];
        }

        $totalWeightedScore = array_sum(array_column($breakdown, 'weighted_score'));
        $finalScore = round($totalWeightedScore, 3);
        $rankTier = self::calculateRank($finalScore);

        return [$finalScore, $rankTier, 'finalized'];
    }

    /**
     * Calculate rank tier based on final score
     */
    protected static function calculateRank(float $score): string
    {
        foreach (self::RANK_THRESHOLDS as $tier => $threshold) {
            if ($score >= $threshold) {
                return $tier;
            }
        }

        return 'none';
    }

    // ========================================
    // DISPLAY HELPERS
    // ========================================

    /**
     * Get rank badge color for UI display
     */
    public function getRankColorAttribute(): string
    {
        return match ($this->rank) {
            'gold' => 'warning',
            'silver' => 'gray',
            'bronze' => 'orange',
            default => 'danger',
        };
    }

    /**
     * Get formatted rank display name
     */
    public function getRankDisplayAttribute(): string
    {
        return match ($this->rank) {
            'gold' => 'Gold',
            'silver' => 'Silver',
            'bronze' => 'Bronze',
            'none' => 'None',
            default => 'Pending',
        };
    }
}
