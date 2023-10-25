<?php

namespace Domain\Vacancy\Models;

use Database\Factories\JobOpportunityFactory;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Builders\JobOpportunityBuilder;
use Domain\Organization\Models\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Support\Traits\Model\HasCreator;
use Support\Traits\Model\HasOwner;

/**
 * @property Organization|null $organization
 */
class JobOpportunity extends Model
{
    use HasFactory,HasOwner,HasCreator,SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'interview_template_id',
        'creator_id',
        'creator_type',
        'started_at',
        'ended_at',
        'max_reconnection_tries',
        'open_positions',
        'organization_id'
    ];

    protected $table = 'Job_opportunities';

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'  => 'datetime'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class,'organization_id');
    }

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class,'interview_template_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class,'interview_id');
    }

    public function newEloquentBuilder($query):JobOpportunityBuilder
    {
        return new JobOpportunityBuilder($query);
    }

    protected static function newFactory(): JobOpportunityFactory
    {
        return new JobOpportunityFactory();
    }
}
