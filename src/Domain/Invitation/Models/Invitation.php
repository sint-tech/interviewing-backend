<?php

namespace Domain\Invitation\Models;

use Carbon\Carbon;
use Database\Factories\InvitationFactory;
use Domain\Candidate\Models\Candidate;
use Domain\Invitation\Builders\InvitationEloquentBuilder;
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
use Support\ValueObjects\MobileNumber;
use Support\ValueObjects\URL;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $should_be_invited_at
 * @property Carbon $last_invited_at
 * @property URL $url
 * @property bool $sent
 * @property bool $is_expired
 * @property MobileNumber $mobile_number
 * @property ?Carbon $expired_at
 * @property ?Carbon $used_at
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
        'vacancy_id',
        'candidate_id',
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

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id', 'id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'email', 'email');
    }

    public function sent(): Attribute
    {
        return Attribute::get(fn () => ! is_null($this->last_invited_at));
    }

    public function mobileNumber(): Attribute
    {
        return Attribute::get(fn () => new MobileNumber($this->mobile_country_code, $this->attributes['mobile_number']));
    }

    public function isExpired(): Attribute
    {
        return Attribute::get(function () {
            return $this->used_at || $this->expired_at?->lessThanOrEqualTo(now()) || $this->vacancy->ended_at?->lessThanOrEqualTo(now());
        });
    }

    public function url(): Attribute
    {
        return Attribute::get(function () {
            return URL::make(
                config('sint.candidate.website_url', 'https://sint.com'),
                [
                    'vacancy_id' => $this->vacancy_id,
                    'invitation_id' => $this->id,
                    'email' => $this->email,
                ]
            );
        });
    }

    public function newEloquentBuilder($query): InvitationEloquentBuilder
    {
        return new InvitationEloquentBuilder($query);
    }

    protected static function newFactory(): InvitationFactory
    {
        return new InvitationFactory();
    }

    protected static function booted(): void
    {
        $authScope = (new ForAuthScope())->forOrganizationEmployee(function (Builder $builder) {
            /*
             * By default the vacancy global scope match the organization id for auth organization
             * So here, only need to ensure each invitation has vacancy
             * the result will be invitations where belongs to vacancy with organization_id = auth->organization_id
             */
            $builder->whereHas('vacancy');
        })->forCandidate(function (Builder $builder) {
            $builder->where('email', auth()->user()->email)
                    ->where('should_be_invited_at', '<=', now())
                    ->whereHas('vacancy', function (Builder $query) {
                        $query->where('started_at', '<=', now());
                    });
        });

        static::addGlobalScope($authScope);
    }
}
