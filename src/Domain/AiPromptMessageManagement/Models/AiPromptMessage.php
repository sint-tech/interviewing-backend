<?php

namespace Domain\AiPromptMessageManagement\Models;

use Domain\AiPromptMessageManagement\Api\AiModelClientInterface;
use Domain\AiPromptMessageManagement\Api\GPT35AiModel;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property AIModel $aiModel
 * @property string $system_prompt
 * @property string $content_prompt
 * @property PromptMessageStatus $status
 * @property int $weight
 */
class AiPromptMessage extends Pivot //change the model path to domain questionVariant
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'ai_model_id',
        'question_variant_id',
        'status',
        'system_prompt',
        'content_prompt',
        'weight',
    ];

    protected $casts = [
        'weight' => 'int',
        'status' => PromptMessageStatus::class,
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(
            QuestionVariant::class,
            'question_variant_id',
            'id'
        );
    }

    public function aiModel(): BelongsTo
    {
        return $this->belongsTo(AIModel::class, 'ai_model_id');
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
