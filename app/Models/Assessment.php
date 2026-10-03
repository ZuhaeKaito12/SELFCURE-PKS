<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'overall_score',
        'overall_level',
        'ai_summary',
        'ai_insights',
        'auditor_notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'ai_insights' => 'array',
        'overall_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function domainScores(): HasMany
    {
        return $this->hasMany(AssessmentDomainScore::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
