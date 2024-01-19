<?php

namespace Domain\InterviewManagement\Models;

use Carbon\Carbon;
use Database\Factories\InterviewFactory;
use Domain\AnswerManagement\Models\AnswerVariant;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Builders\InterviewEloquentBuilder;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionClusterRecommendation;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\ReportManagement\Models\InterviewReport;
use Domain\ReportManagement\Traits\HasReport;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Support\Scopes\ForAuthScope;
use Support\ValueObjects\URL;

/**
 * @property Collection<QuestionCluster> $questionClusters
 * @property Collection<QuestionVariant> $questionVariants
 * @property Collection<Answer> $answers
 * @property ?Carbon $candidate_report_sent_at
 */
class Interview extends Model
{
    use HasFactory,SoftDeletes,HasReport;

    protected $fillable = [
        'vacancy_id',
        'interview_template_id',
        'candidate_id',
        'started_at',
        'ended_at',
        'status',
        'connection_tries',
        'candidate_report_sent_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'status' => InterviewStatusEnum::class,
    ];

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function interviewTemplateQuestions(): HasMany
    {
        return $this->hasMany(
            InterviewTemplateQuestion::class,
            'interview_template_id',
            'interview_template_id'
        );
    }

    public function questionVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionVariant::class,
            'interview_template_questions',
            'interview_template_id',
            'question_variant_id',
            'interview_template_id'

        )
            ->using(InterviewTemplateQuestion::class)
            ->withPivot('question_cluster_id')
            ->withTimestamps();
    }

    public function answers(): HasMany
    {
        $base_recommendation_query = QuestionClusterRecommendation::query()
            ->limit(1)
            ->select('statement')
            ->whereColumn('interview_answers.score', '>=', 'question_cluster_recommendations.min_score')
            ->whereColumn('interview_answers.score', '<=', 'question_cluster_recommendations.max_score')
            ->whereColumn('question_cluster_recommendations.question_cluster_id', 'interview_answers.question_cluster_id');

        return $this->hasMany(
            Answer::class,
            'interview_id',
            'id'
        )
            ->addSelect([
                'advice_statement' => $base_recommendation_query->clone()->whereTypeIsAdvice(),
                'impact_statement' => $base_recommendation_query->clone()->whereTypeIsImpact(),
            ])
            ->withCasts([
                'advice_statement' => 'string',
                'impact_statement' => 'string',
            ]);
    }

    public function answerVariants(): BelongsToMany
    {
        $pivot_columns = Schema::getColumnListing('interview_answers');

        return $this->belongsToMany(
            AnswerVariant::class,
            'interview_answers',
            'interview_id',
            'answer_variant_id',
            'id'
        )
            ->using(Answer::class)
            ->withPivot($pivot_columns)
            ->withTimestamps();
    }

    public function questionClusters(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionCluster::class,
            'interview_template_questions',
            'interview_template_id',
            'question_cluster_id',
            'interview_template_id'

        )
            ->using(InterviewTemplateQuestion::class)
            ->withPivot('question_variant_id')
            ->withTimestamps();
    }

    public function defaultLastReport(): MorphOne
    {
        return $this->morphOne(InterviewReport::class, 'reportable')
            ->where('name', InterviewReport::DEFAULT_REPORT_NAME)
            ->latest();
    }

    public function allQuestionsAnswered(): bool
    {
        return $this->questionVariants()->count() ==
            $this->answers()
                ->whereIn('question_variant_id',
                    $this->questionVariants()
                        ->select('question_variants.id')
                )
                ->count();
    }

    public function running(): bool
    {
        return is_null($this->ended_at);
    }

    public function ended(): bool
    {
        return ! $this->running();
    }

    public function passed(): bool
    {
        return $this->statusIs(InterviewStatusEnum::Passed);
    }

    public function statusIs(InterviewStatusEnum $status): bool
    {
        return $this->status === $status;
    }

    public function statusInFinalStage(): bool
    {
        return in_array($this->status, InterviewStatusEnum::endedStatuses());
    }

    public function candidateReportUrl(): Attribute
    {
        return Attribute::get(function () {
            return URL::make(config('sint.candidate.report_url') . '/');
        });
    }

    public function newEloquentBuilder($query)
    {
        return new InterviewEloquentBuilder($query);
    }

    protected static function booted(): void
    {
        $scope = new ForAuthScope();

        $scope->forCandidate(fn (InterviewEloquentBuilder $builder) => $builder->whereCandidate(auth()->user()));

        $scope->forOrganizationEmployee(fn (InterviewEloquentBuilder $builder) => $builder->whereHas('vacancy'));

        static::addGlobalScope($scope);
    }

    protected static function newFactory()
    {
        return new InterviewFactory();
    }
}
