<?php

namespace Domain\InterviewManagement\Models;

use Domain\AnswerManagement\Models\AnswerVariant;
use Domain\Candidate\Models\Candidate;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionClusterRecommendation;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\ReportManagement\Traits\HasReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * @property Collection<QuestionCluster> $questionClusters
 * @property Collection<QuestionVariant> $questionVariants
 * @property Collection<Answer> $answers
 */
class Interview extends Model
{
    use HasFactory,SoftDeletes,HasReport;

    protected $fillable = [
        'interview_template_id',
        'candidate_id',
        'started_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function interviewTemplate(): BelongsTo
    {
        return $this->belongsTo(InterviewTemplate::class, 'interview_template_id');
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
                'advice_statement' => (clone $base_recommendation_query)->whereTypeIsAdvice(),
                'impact_statement' => (clone $base_recommendation_query)->whereTypeIsImpact(),
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
}
