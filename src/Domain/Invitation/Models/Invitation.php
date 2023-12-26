<?php

namespace Domain\Invitation\Models;

use Carbon\Carbon;
use Database\Factories\InvitationFactory;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Support\Scopes\ForAuthScope;
use Support\Traits\Model\HasBatch;
use Support\Traits\Model\HasCreator;
use Support\ValueObjects\URL;

/**
 * @property Carbon $should_be_invited_at
 * @property Carbon $last_invited_at
 * @property URL $url
 * @property bool $sent
 */
class Invitation extends Model
{
    use HasFactory,HasBatch,SoftDeletes,HasCreator;

    protected $fillable = [
        'name',
        'email',
        'mobile_country_code',
        'mobile_number',
        'batch',
        'expired_at',
        'interview_template_id',
        'vacancy_id',
        'should_be_invited_at',
        'last_invited_at',
        'used_at',
        'creator_id',
        'creator_type',
    ];

    protected $casts = [
        'should_be_invited_at' => 'datetime',
        'last_invited_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /**
     * @deprecated
     */
    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id', 'id');
    }

    public function sent(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->last_invited_at));
    }

    public function url(): Attribute
    {
        return Attribute::get(function () {
            return URL::make(
                config('sint.candidate.website_url', 'https://sint.com'),
                [
                    'vacancy_id' => $this->vacancy_id,
                    'invitation_id' => $this->getKey(),
                    'email' => $this->email,
                    'first_name' => str($this->first_name)->before(' ')->toString(),
                    'last_name' => str($this->last_name)->afterLast(' ')->toString(),
                ]
            );
        });
    }

    protected static function newFactory(): InvitationFactory
    {
        return new InvitationFactory();
    }

    protected static function booted(): void
    {
        static::addGlobalScope((new ForAuthScope())->forOrganizationEmployee(function (Builder $builder) {
            /*
             * vacancy global scope will bound,
             * the final query will contain
             * `where has vacancies where organization_id = (current employee organizational id)`
             */
            $builder->whereHas('vacancy');
        }));
    }
}
