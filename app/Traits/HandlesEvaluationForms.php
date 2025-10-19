<?php

namespace App\Traits;

use App\Models\Evaluation;
use Filament\Notifications\Notification;

/**
 * Shared evaluation form logic for both admin and student evaluation pages
 */
trait HandlesEvaluationForms
{
    /** Domain number to title mapping */
    protected const DOMAIN_TITLES = [
        '1' => 'Domain 1: Paulinian Leadership as Social Responsibility',
        '2' => 'Domain 2: Paulinian Leadership as a Life of Service',
        '3' => 'Domain 3: Paulinian Leader as Leading by Example',
    ];

    /**
     * Group questions by domain and strand for organized display
     */
    public function groupQuestions(array $questions): array
    {
        if (empty($questions)) {
            return [];
        }

        $grouped = [];

        foreach ($questions as $key => $question) {
            // If question is an array, extract text
            $text = is_array($question) ? ($question['text'] ?? '') : $question;
            if ($key === 'length_of_service') {
                $grouped['Length of Service']['Service Duration'][$key] = $text;
                continue;
            }
            $this->categorizeQuestion($key, $text, $grouped);
        }

        return $grouped;
    }

    /**
     * Categorize a single question into its domain and strand
     */
    protected function categorizeQuestion(string $key, string $text, array &$grouped): void
    {
        $parts = explode('_', $key);

        if (count($parts) >= 4) {
            $domainNum = $parts[1];
            $strandNum = $parts[3];

            $domainName = self::DOMAIN_TITLES[$domainNum] ?? "Domain {$domainNum}";
            $strandName = "Strand {$strandNum}";

            $grouped[$domainName][$strandName][$key] = $text;
        }
    }

    /**
     * Validate that all required questions have answers
     */
    protected function validateAnswers(array $questions): bool
    {
        $unansweredQuestions = array_filter(
            array_keys($questions),
            fn($key) => !isset($this->data[$key]) || $this->data[$key] === '' || $this->data[$key] === null
        );

        if (!empty($unansweredQuestions)) {
            Notification::make()
                ->title('Please answer all questions before submitting.')
                ->danger()
                ->send();
            return false;
        }

        return true;
    }

    /**
     * Update existing evaluation record
     */
    protected function updateExistingEvaluation(array $data): void
    {
        $this->evaluation->update(['answers' => $data]);
    }

    /**
     * Send success notification to user
     */
    protected function sendSuccessNotification(): void
    {
        Notification::make()
            ->title('Evaluation saved successfully!')
            ->success()
            ->send();
    }
}