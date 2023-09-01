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
 * @property AiModelEnum $ai_model
 */
class AiPromptMessage extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'prompt_text',
        'ai_model',
        'question_variant_id',
        'is_default',
    ];

    protected $casts = [
        'ai_model' => AiModelEnum::class,
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(
            QuestionVariant::class,
            'question_variant_id',
            'id'
        );
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->ai_model) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
