<?php

namespace Domain\QuestionManagement\Models;

use Database\Factories\QuestionFactory;
use Domain\AiPromptMessageManagement\Models\AIModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    const DEFAULT_MIN_READING_DURATION_IN_SECONDS = 10;

    const DEFAULT_MAX_READING_DURATION_IN_SECONDS = 120;

    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'creator_type',
        'question_cluster_id',
        'question_type',
        'difficult_level',
        'min_reading_duration_in_seconds',
        'max_reading_duration_in_seconds',
        'default_ai_model_id',
    ];

    public function questionCluster(): BelongsTo
    {
        return $this->belongsTo(QuestionCluster::class, 'question_cluster_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    public function questionVariants(): HasMany
    {
        return $this->hasMany(QuestionVariant::class, 'question_id');
    }

    public function defaultAiModel(): BelongsTo
    {
        return $this->belongsTo(AIModel::class, 'default_ai_model_id');
    }

    protected static function newFactory()
    {
        return new QuestionFactory;
    }
}
