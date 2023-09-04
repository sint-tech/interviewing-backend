<?php

namespace Domain\AiPromptMessageManagement\Models;

use Domain\AiPromptMessageManagement\Api\AiModelClientInterface;
use Domain\AiPromptMessageManagement\Api\GPT35AiModel;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property AIModel $ai_model
 */
class AiPromptMessage extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'ai_model_id',
        'question_variant_id',
        'is_default',
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(
            QuestionVariant::class,
            'question_variant_id',
            'id'
        );
    }

    public function aiModel():BelongsTo
    {
        return $this->belongsTo(AIModel::class,'ai_model_id');
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->ai_model->name) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
