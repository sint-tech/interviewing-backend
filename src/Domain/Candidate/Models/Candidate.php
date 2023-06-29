<?php

namespace Domain\Candidate\Models;

use Database\Factories\CandidateFactory;
use Domain\Candidate\Builders\CandidateBuilder;
use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Domain\InterviewManagement\Models\Interview;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Candidate extends Authenticatable implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use HasApiTokens;
    use InteractsWithMedia;

    protected $guard = 'candidate';

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'mobile_country',
        'mobile_country_code',
        'mobile_number',
        'password',
        'social_driver_name',
        'social_driver_id',
        'current_job_title_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'social_driver_name' => CandidateSocialAppEnum::class,
    ];

    public function registeredWithSocialApp(): bool
    {
        return $this->social_driver_name && $this->social_driver_id;
    }

    public function currentJobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'current_job_title_id');
    }

    public function interviews():HasMany
    {
        return $this->hasMany(Interview::class,'candidate_id');
    }

    public function desireHiringPositions(): BelongsToMany
    {
        return $this->belongsToMany(JobTitle::class, CandidateHiringPosition::class, 'candidate_id')
            ->withTimestamps();
    }

    public function registrationReasons(): BelongsToMany
    {
        return $this->belongsToMany(RegistrationReason::class, CandidateRegistrationReason::class, 'candidate_id')
            ->withTimestamps();
    }

    public function newEloquentBuilder($query): CandidateBuilder
    {
        return new CandidateBuilder($query);
    }

    protected static function newFactory()
    {
        return new CandidateFactory();
    }
}
