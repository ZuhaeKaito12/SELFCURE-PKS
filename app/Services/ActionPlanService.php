<?php

namespace App\Services;

use App\Models\ActionPlanItem;
use App\Models\Assessment;
use App\Models\User;
use Illuminate\Support\Collection;

class ActionPlanService
{
    /**
     * Assessment -> gap -> action. Every answer at or below the gap
     * threshold adds (or reopens) the matching action on the owner's
     * roadmap; an action whose practice the new assessment shows is now
     * in place is marked completed.
     */
    public function syncFromAssessment(Assessment $assessment): void
    {
        $answers = $assessment->answers()->with('question.action')->get()
            ->filter(fn ($answer) => $answer->question?->action);

        foreach ($answers as $answer) {
            $action = $answer->question->action;

            if ($answer->score <= ComplianceReportService::GAP_THRESHOLD) {
                $item = ActionPlanItem::firstOrNew([
                    'user_id' => $assessment->user_id,
                    'security_action_id' => $action->id,
                ]);
                $item->setRelation('action', $action);

                if (! $item->exists || $item->isCompleted()) {
                    $item->setStatus('not_started');
                }

                $item->assessment_id = $assessment->id;
                $item->answer_score = $answer->score;
                $item->save();

                continue;
            }

            $item = ActionPlanItem::where('user_id', $assessment->user_id)
                ->where('security_action_id', $action->id)
                ->where('status', '!=', 'completed')
                ->first();

            if ($item) {
                $item->setRelation('action', $action);
                $item->setStatus('completed');
                $item->save();
            }
        }
    }

    /**
     * Open actions first, most urgent first, then by framework order.
     */
    public function itemsFor(User $user): Collection
    {
        return ActionPlanItem::where('user_id', $user->id)
            ->with(['action.question.domain', 'assessment'])
            ->get()
            ->sortBy([
                fn ($a, $b) => $a->isCompleted() <=> $b->isCompleted(),
                fn ($a, $b) => ($a->priority() === 'high' ? 0 : 1) <=> ($b->priority() === 'high' ? 0 : 1),
                fn ($a, $b) => $a->action->question->domain->order <=> $b->action->question->domain->order,
                fn ($a, $b) => $a->action->question->order <=> $b->action->question->order,
            ])
            ->values();
    }

    /** @return array{total:int, completed:int, in_progress:int, not_started:int, percent:int} */
    public function summary(Collection $items): array
    {
        $total = $items->count();
        $completed = $items->where('status', 'completed')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $items->where('status', 'in_progress')->count(),
            'not_started' => $items->where('status', 'not_started')->count(),
            'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
        ];
    }
}
