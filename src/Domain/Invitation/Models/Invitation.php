<?php

namespace Domain\Invitation\Models;

use Database\Factories\InvitationFactory;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Support\Scopes\ForAuthScope;

class Invitation extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile_country_code',
        'mobile_number',
        'batch',
        'should_be_invited_at',
        'interview_template_id',
        'vacancy_id',
        'last_invited_at',
        'expired_at',
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

    public function invitationSent(): bool
    {
        return ! is_null($this->last_invited_at);
    }

    protected static function newFactory(): InvitationFactory
    {
        return new InvitationFactory();
    }

    protected static function booted(): void
    {
        static::addGlobalScope((new ForAuthScope())->forOrganizationEmployee(function (Builder $builder) {
            //vacancy global scope will bound, so need to check
            $builder->whereHas('vacancy');
        }));
    }
}
