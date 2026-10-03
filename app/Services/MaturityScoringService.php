<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentDomainScore;
use App\Models\Domain;

class MaturityScoringService
{
    /**
     * The 1-5 Likert scale used across every question in the questionnaire.
     * Score 1 = weakest practice, Score 5 = strongest / fully in place.
     *
     * @return array<int, string>
     */
    public static function scaleLabels(): array
    {
        return [
            1 => __('Never / Not in place'),
            2 => __('Rarely'),
            3 => __('Sometimes / Partially in place'),
            4 => __('Usually / Mostly in place'),
            5 => __('Always / Fully in place and documented'),
        ];
    }

    /**
     * Convert an average raw score (1.0 - 5.0) into a 0-100 percentage.
     */
    public function toPercentage(float $averageScore): float
    {
        return round((($averageScore - 1) / 4) * 100, 2);
    }

    /**
     * Determine the maturity level label for a given percentage score.
     */
    public function levelForPercentage(float $percentage): string
    {
        return match (true) {
            $percentage < 40 => 'Initial',
            $percentage < 65 => 'Developing',
            $percentage < 85 => 'Managed',
            default => 'Optimized',
        };
    }

    /**
     * Human readable description of what a maturity level means, for the
     * benefit of a non-technical SME owner reading their report.
     */
    public function levelDescription(string $level): string
    {
        return match ($level) {
            'Initial' => __('Cybersecurity practices in this area are largely missing or ad-hoc. This represents the highest risk and should be prioritised first.'),
            'Developing' => __('Some practices exist but are inconsistent or undocumented. There are meaningful gaps that should be addressed soon.'),
            'Managed' => __('Practices are mostly in place and followed consistently, with room for minor improvement.'),
            'Optimized' => __('Strong, consistent practices are in place. Continue to monitor and review regularly.'),
            default => '',
        };
    }

    /**
     * Simplified two-state evaluation used in the domain gap breakdown table.
     */
    public function evaluationStatusForPercentage(float $percentage): string
    {
        return $percentage >= 65 ? __('Satisfying') : __('Needs Improvement');
    }

    /**
     * Business-facing cyber risk classification, used by the administrator
     * portal to flag SMEs that need attention.
     */
    public function riskLevelForPercentage(?float $percentage): string
    {
        if ($percentage === null) {
            return 'Not Assessed';
        }

        return match (true) {
            $percentage < 40 => 'High Risk',
            $percentage < 65 => 'Medium Risk',
            default => 'Low Risk',
        };
    }

    /**
     * Per-question evaluation label shown in the administrator's detailed
     * audit breakdown.
     */
    public function questionStatusForScore(int $score): string
    {
        return match (true) {
            $score <= 2 => 'Basic',
            $score === 3 => 'Developing',
            default => 'Satisfying',
        };
    }

    /**
     * Recalculate and persist the per-domain scores and the overall score
     * for a given assessment, based on its currently saved answers.
     */
    public function calculateAndStore(Assessment $assessment): Assessment
    {
        $answers = $assessment->answers()->with('question.domain')->get();

        $domains = Domain::all();
        $domainAverages = [];

        foreach ($domains as $domain) {
            $domainAnswers = $answers->filter(
                fn ($answer) => $answer->question->domain_id === $domain->id
            );

            if ($domainAnswers->isEmpty()) {
                continue;
            }

            $average = $domainAnswers->avg('score');
            $percentage = $this->toPercentage($average);
            $level = $this->levelForPercentage($percentage);

            AssessmentDomainScore::updateOrCreate(
                ['assessment_id' => $assessment->id, 'domain_id' => $domain->id],
                ['score' => $percentage, 'level' => $level]
            );

            $domainAverages[] = $percentage;
        }

        $overallPercentage = count($domainAverages) > 0
            ? round(array_sum($domainAverages) / count($domainAverages), 2)
            : 0;

        $assessment->update([
            'overall_score' => $overallPercentage,
            'overall_level' => $this->levelForPercentage($overallPercentage),
        ]);

        return $assessment->fresh(['domainScores.domain']);
    }
}
