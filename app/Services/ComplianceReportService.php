<?php

namespace App\Services;

use App\Models\Assessment;
use Illuminate\Support\Collection;

class ComplianceReportService
{
    /**
     * Threshold at or below which an answer is treated as a security gap
     * worth flagging in the report (on the 1-5 answer scale).
     */
    public const GAP_THRESHOLD = 2;

    /**
     * Build a prioritised, plain-language list of gaps and recommendations
     * for a completed assessment. Weakest answers (score 1) are listed
     * before moderate gaps (score 2), and within the same score the
     * lowest-scoring domain is prioritised first.
     */
    public function prioritisedRecommendations(Assessment $assessment): Collection
    {
        $domainScoreByDomainId = $assessment->domainScores->keyBy('domain_id');

        return $assessment->answers
            ->load('question.domain', 'question.action')
            ->filter(fn ($answer) => $answer->score <= self::GAP_THRESHOLD)
            ->map(function ($answer) use ($domainScoreByDomainId) {
                $question = $answer->question;
                $action = $question->action;
                $domainScore = $domainScoreByDomainId->get($question->domain_id);

                return [
                    'domain_code' => $question->domain->code,
                    'domain_name' => $question->domain->translatedName(),
                    'domain_score' => $domainScore?->score ?? 0,
                    'question_text' => $question->translatedText(),
                    'answer_score' => $answer->score,
                    'priority' => $answer->score <= 1 ? 'high' : 'medium',
                    'action_id' => $action?->id,
                    'action_title' => $action?->translatedTitle() ?? $question->translatedText(),
                    'action_description' => $action?->translatedDescription() ?? $question->translatedRecommendation(),
                    'topic' => $action?->translatedTopic() ?? $question->domain->translatedName(),
                    'finding' => $action?->translatedFinding() ?? $question->translatedGapDescription(),
                    'steps' => $action?->translatedSteps() ?? [],
                    'gap_description' => $question->translatedGapDescription(),
                    'recommendation' => $question->translatedRecommendation(),
                    'regulation_reference' => $question->regulation_reference,
                ];
            })
            ->sortBy([
                ['answer_score', 'asc'],
                ['domain_score', 'asc'],
            ])
            ->values();
    }

    /**
     * Practices the SME already has firmly in place (answered 4 or 5),
     * strongest first, described by their topic rather than the question.
     */
    public function strengths(Assessment $assessment): Collection
    {
        return $assessment->answers
            ->load('question.domain', 'question.action')
            ->filter(fn ($answer) => $answer->score >= 4)
            ->sortBy([
                ['score', 'desc'],
                fn ($a, $b) => $a->question->domain->order <=> $b->question->domain->order,
            ])
            ->map(fn ($answer) => [
                'topic' => $answer->question->action?->translatedTopic() ?? $answer->question->translatedText(),
                'domain_name' => $answer->question->domain->translatedName(),
                'score' => $answer->score,
            ])
            ->values();
    }
}
