<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One action on an SME's roadmap: a SecurityAction generated from a gap
 * in one of their assessments, with their own progress on it.
 */
class ActionPlanItem extends Model
{
    public const STATUSES = ['not_started', 'in_progress', 'completed'];

    protected $fillable = [
        'user_id',
        'security_action_id',
        'assessment_id',
        'answer_score',
        'status',
        'completed_steps',
        'completed_at',
    ];

    protected $casts = [
        'completed_steps' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(SecurityAction::class, 'security_action_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /** A score of 1 ("never / not in place") is more urgent than a 2. */
    public function priority(): string
    {
        return ($this->answer_score ?? 2) <= 1 ? 'high' : 'medium';
    }

    public function stepCount(): int
    {
        return count($this->action->steps ?? []);
    }

    /** @return array<int, int> */
    public function doneSteps(): array
    {
        return array_values(array_filter(
            $this->completed_steps ?? [],
            fn ($index) => $index >= 0 && $index < $this->stepCount()
        ));
    }

    public function progressPercent(): int
    {
        if ($this->isCompleted()) {
            return 100;
        }

        $total = $this->stepCount();

        return $total > 0 ? (int) round(count($this->doneSteps()) / $total * 100) : 0;
    }

    /**
     * Keep status and the step checklist consistent with each other when
     * either one changes.
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->completed_at = $status === 'completed' ? ($this->completed_at ?? now()) : null;

        if ($status === 'completed') {
            $this->completed_steps = range(0, max($this->stepCount() - 1, 0));
        } elseif ($status === 'not_started') {
            $this->completed_steps = [];
        }
    }

    public function toggleStep(int $index, bool $done): void
    {
        $steps = collect($this->doneSteps());
        $steps = $done ? $steps->push($index)->unique() : $steps->reject(fn ($i) => $i === $index);
        $this->completed_steps = $steps->sort()->values()->all();

        $count = count($this->completed_steps);

        if ($count === $this->stepCount()) {
            $this->status = 'completed';
            $this->completed_at ??= now();
        } else {
            $this->status = $count > 0 ? 'in_progress' : 'not_started';
            $this->completed_at = null;
        }
    }
}
