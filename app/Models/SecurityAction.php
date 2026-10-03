<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A concrete task an SME can carry out to close the gap revealed by one
 * assessment question. The question measures current practice; this is
 * what to do about it.
 */
class SecurityAction extends Model
{
    protected $fillable = [
        'question_id',
        'title',
        'title_ms',
        'topic',
        'topic_ms',
        'finding',
        'finding_ms',
        'description',
        'description_ms',
        'steps',
        'steps_ms',
    ];

    protected $casts = [
        'steps' => 'array',
        'steps_ms' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function planItems(): HasMany
    {
        return $this->hasMany(ActionPlanItem::class);
    }

    public function translatedTitle(): string
    {
        return $this->localized('title');
    }

    public function translatedTopic(): string
    {
        return $this->localized('topic');
    }

    public function translatedFinding(): string
    {
        return $this->localized('finding');
    }

    public function translatedDescription(): string
    {
        return $this->localized('description');
    }

    /** @return array<int, string> */
    public function translatedSteps(): array
    {
        return app()->getLocale() === 'ms' && ! empty($this->steps_ms) ? $this->steps_ms : $this->steps;
    }

    private function localized(string $field): string
    {
        $ms = $field.'_ms';

        return app()->getLocale() === 'ms' && $this->{$ms} ? $this->{$ms} : $this->{$field};
    }
}
