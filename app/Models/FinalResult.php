<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinalResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Update or create final result for a student
     */
    public static function updateForStudent(int $organizationId, int $studentId): void
    {
        $evaluations = Evaluation::where('organization_id', $organizationId)
            ->where('student_id', $studentId)
            ->get()
            ->keyBy('evaluator_type');

        $finalResult = self::firstOrCreate([
            'organization_id' => $organizationId,
            'student_id' => $studentId,
        ]);

        // Calculate breakdown
        $breakdown = [];
        $totalWeightedScore = 0;
        $totalWeight = 0;

        // Weight configuration
        $weights = [
            'adviser' => 0.65, // 50% + 15% (includes length of service)
            'peer' => 0.25,
            'self' => 0.10,
        ];

        foreach ($weights as $evaluatorType => $weight) {
            if (isset($evaluations[$evaluatorType])) {
                $score = $evaluations[$evaluatorType]->evaluator_score;
                $breakdown[$evaluatorType] = [
                    'score' => $score,
                    'weight' => $weight,
                    'weighted_score' => $score * $weight,
                ];
                $totalWeightedScore += $score * $weight;
                $totalWeight += $weight;
            }
        }

        // Determine if finalized (all required evaluations present)
        $isFinalized = isset($evaluations['adviser']) && 
                      isset($evaluations['peer']) && 
                      isset($evaluations['self']);

        if ($isFinalized) {
            $finalScore = round($totalWeightedScore, 3);
            $rank = self::calculateRank($finalScore);
            $status = 'finalized';
        } else {
            $finalScore = null;
            $rank = null;
            $status = 'pending';
        }

        $finalResult->update([
            'final_score' => $finalScore,
            'rank' => $rank,
            'status' => $status,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Calculate rank based on final score
     */
    private static function calculateRank(float $score): string
    {
        if ($score >= 2.41) {
            return 'gold';
        } elseif ($score >= 1.81) {
            return 'silver';
        } elseif ($score >= 1.21) {
            return 'bronze';
        } else {
            return 'none';
        }
    }

    /**
     * Get rank badge color
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
     * Get rank display name
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
