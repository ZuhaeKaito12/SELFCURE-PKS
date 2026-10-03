<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_name',
        'ssm_number',
        'business_type',
        'state',
        'phone',
        'two_factor_enabled',
        'has_seen_tour',
    ];

    /**
     * Notifications are stored without a foreign key (they are polymorphic),
     * so they are removed here when an account is deleted, either by the owner
     * from the Profile page or by an administrator. Assessments, answers and
     * action items already cascade through their foreign keys.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $user->notifications()->delete();
        });
    }

    public const BUSINESS_TYPES = [
        'Food & Beverage',
        'Retail & E-commerce',
        'Professional Services',
        'Manufacturing',
        'Agriculture',
        'Tourism & Hospitality',
        'Other',
    ];

    public const STATES = [
        'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 'Pahang',
        'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 'Sarawak', 'Selangor',
        'Terengganu', 'W.P. Kuala Lumpur', 'W.P. Labuan', 'W.P. Putrajaya',
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function latestCompletedAssessment(): HasOne
    {
        return $this->hasOne(Assessment::class)
            ->where('status', 'completed')
            ->latestOfMany('completed_at');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSmeOwner(): bool
    {
        return $this->role === 'sme_owner';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'has_seen_tour' => 'boolean',
        ];
    }
}
