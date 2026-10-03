<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'name_ms',
        'description_ms',
        'order',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function domainScores(): HasMany
    {
        return $this->hasMany(AssessmentDomainScore::class);
    }

    public function translatedName(): string
    {
        return app()->getLocale() === 'ms' && $this->name_ms ? $this->name_ms : $this->name;
    }

    public function translatedDescription(): string
    {
        return app()->getLocale() === 'ms' && $this->description_ms ? $this->description_ms : $this->description;
    }
}
