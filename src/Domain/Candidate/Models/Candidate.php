<?php

namespace Domain\Candidate\Models;

use Database\Factories\CandidateFactory;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\Types\This;

class Candidate extends Authenticatable
{
    use HasFactory,SoftDeletes,HasApiTokens;

    protected $guard = "candidate";

    protected $fillable = [
        "first_name",
        "last_name",
        "full_name",
        "email",
        "mobile_country",
        "mobile_country_code",
        "mobile_number",
        "password",
        "current_job_title_id"
    ];

    public function currentJobTitle():BelongsTo
    {
        return $this->belongsTo(JobTitle::class,"current_job_title_id");
    }

    public function desireHiringPositions():BelongsToMany
    {
        return $this->belongsToMany(JobTitle::class,CandidateHiringPosition::class,"candidate_id");
    }

    protected static function newFactory()
    {
        return (new CandidateFactory());
    }
}
