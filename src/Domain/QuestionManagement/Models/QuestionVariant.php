<?php

namespace Domain\QuestionManagement\Models;

use Database\Factories\QuestionVariantFactory;
use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\AiPromptMessageManagement\Models\AiPromptMessage;
use Domain\AnswerManagement\Models\Answer;
use Domain\AnswerManagement\Models\AnswerVariant;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasOneDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Support\Scopes\ForAuthScope;

/**
 * @property AiPromptMessage $defaultAiPromptMessage
 */
class QuestionVariant extends Model
{
    use HasFactory,SoftDeletes,HasRelationships;

    protected $table = 'question_variants';

    protected $fillable = [
        'text',
        'description',
        'reading_time_in_seconds',
        'answering_time_in_seconds',
        'question_id',
        'creator_id',
        'creator_type',
        'organization_id',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function questionCluster(): HasOneDeep
    {
        return $this->hasOneDeepFromRelations($this->question(), (new Question())->questionCluster());
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'question_variant_id');
    }

    public function answerVariants(): HasManyThrough
    {
        return $this->hasManyThrough(
            AnswerVariant::class,
            Answer::class,
            'question_variant_id',
            'answer_variant_id',
            'id',
            'id'
        );
    }

    public function interviewTemplates(): BelongsToMany
    {
        return $this->belongsToMany(
            InterviewTemplate::class,
            'interview_template_questions',
            'question_variant_id',
            'interview_template_id',
        );
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    public function aiPromptMessages(): HasMany
    {
        return $this->hasMany(
            AiPromptMessage::class,
            'question_variant_id',
            'id'
        );
    }

    public function aiModels(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                AIModel::class,
                'ai_prompt_messages',
                'question_variant_id',
                'ai_model_id',
            )->withTimestamps()
            ->withPivot(['is_default']);
    }

    public function defaultAiPromptMessage(): HasOne
    {
        return $this->hasOne(
            AiPromptMessage::class,
            'question_variant_id',
            'id'
        )
            ->where('is_default', true)
            ->ofMany();
    }

    protected static function newFactory()
    {
        return new QuestionVariantFactory;
    }

    protected static function booted()
    {
        $scope = new ForAuthScope();

        $scope->forOrganizationEmployee(function (Builder $builder) {
            return $builder->where('organization_id', auth()->user()->organization_id);
        });

        static::addGlobalScope($scope);
    }
}
