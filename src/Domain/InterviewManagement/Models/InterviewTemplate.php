<?php

namespace Domain\InterviewManagement\Models;

use Database\Factories\InterviewTemplateFactory;
use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\JobTitle\Models\JobTitle;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\QuestionManagement\Models\QuestionCluster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Support\Scopes\ForAuthScope;
use Support\Traits\Model\PreventDeleteWithRelations;
use Domain\Vacancy\Models\Vacancy;

class InterviewTemplate extends Model
{
    use HasFactory,SoftDeletes,NodeTrait,PreventDeleteWithRelations;

    protected $fillable = [
        'name',
        'description',
        'availability_status',
        'organization_id',
        'targeted_job_title_id',
        'parent_id',
        'creator_id',
        'creator_type',
        'reusable',
    ];

    protected $casts = [
        'availability_status' => InterviewTemplateAvailabilityStatusEnum::class,
    ];

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'interview_template_id');
    }

    public function endedInterviews(): HasMany
    {
        return $this->interviews()
            ->whereStatusInFinalStage()
            ->whereIsEnded();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function targetedJobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'targeted_job_title_id');
    }

    public function jobProfile(): BelongsTo
    {
        return $this->targetedJobTitle();
    }

    public function jobTitle(): BelongsTo
    {
        return $this->targetedJobTitle();
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'interview_template_id');
    }

    public function hasRunningVacancies(): bool
    {
        return $this->vacancies()->whereRunning()->exists();
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function questionVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionVariant::class,
            'interview_template_questions',
            'interview_template_id',
            'question_variant_id'
        )->withTimestamps()
            ->withPivot('order')
            ->orderBy('order');
    }

    public function questionClusters(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionCluster::class,
            'interview_template_questions',
            'interview_template_id',
            'question_cluster_id'
        )->withTimestamps()->distinct();
    }
    protected function getPreventDeletionRelations(): array
    {
        return [
            'interviews',
            'vacancies',
        ];
    }

    protected static function newFactory(): InterviewTemplateFactory
    {
        return new InterviewTemplateFactory();
    }

    protected static function booted(): void
    {
        $scope = (new ForAuthScope());

        parent::addGlobalScope(
            $scope->forOrganizationEmployee(
                function (Builder $builder) {
                    return $builder->where('organization_id', auth()->user()->organization_id);
                })
        );
    }
}
