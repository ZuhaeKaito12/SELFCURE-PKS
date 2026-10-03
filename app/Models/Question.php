<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'order',
        'text',
        'gap_description',
        'recommendation',
        'regulation_reference',
        'text_ms',
        'gap_description_ms',
        'recommendation_ms',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function action(): HasOne
    {
        return $this->hasOne(SecurityAction::class);
    }

    public function translatedText(): string
    {
        return app()->getLocale() === 'ms' && $this->text_ms ? $this->text_ms : $this->text;
    }

    public function translatedGapDescription(): string
    {
        return app()->getLocale() === 'ms' && $this->gap_description_ms ? $this->gap_description_ms : $this->gap_description;
    }

    public function translatedRecommendation(): string
    {
        return app()->getLocale() === 'ms' && $this->recommendation_ms ? $this->recommendation_ms : $this->recommendation;
    }
}
