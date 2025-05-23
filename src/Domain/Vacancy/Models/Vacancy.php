<?php

namespace Domain\Vacancy\Models;

use Carbon\Carbon;
use Database\Factories\VacancyFactory;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Invitation\Models\Invitation;
use Domain\Organization\Models\Organization;
use Domain\Vacancy\Builders\VacancyBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Support\Scopes\ForAuthScope;
use Support\Traits\Model\HasCreator;
use Support\Traits\Model\HasOwner;
use Support\Traits\Model\PreventDeleteWithRelations;
use DateTimeInterface;
use Illuminate\Support\Str;

/**
 * @property bool $is_ended
 * @property Carbon|null $ended_at
 * @property Organization|null $organization
 */
class Vacancy extends Model
{
    use HasFactory, HasOwner, HasCreator, SoftDeletes, PreventDeleteWithRelations;

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
        'organization_id',
        'is_public',
        'slug',
    ];

    protected $table = 'vacancies';

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function defaultInterviewTemplate(): BelongsTo
    {
        return $this->interviewTemplate();
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'vacancy_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'vacancy_id');
    }

    public function newEloquentBuilder($query): VacancyBuilder
    {
        return new VacancyBuilder($query);
    }

    protected static function newFactory(): VacancyFactory
    {
        return new VacancyFactory();
    }

    public function isEnded(): Attribute
    {
        return Attribute::get(fn () => $this->ended_at?->lessThanOrEqualTo(now()));
    }

    public function isStarted(): Attribute
    {
        return Attribute::get(fn () => $this->started_at?->lessThanOrEqualTo(now()));
    }

    protected function getPreventDeletionRelations(): array
    {
        return [
            'interviews',
        ];
    }

    protected static function booted(): void
    {
        $scope = new ForAuthScope();

        $scope->forOrganizationEmployee(
            fn (VacancyBuilder $builder) => $builder->forAuth()
        );

        static::addGlobalScope($scope);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function generateSlug(): string
    {
        $slug = Str::slug($this->title);

        $original_slug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = "$original_slug-$counter";
            $counter++;
        }

        return $slug;
    }
}
