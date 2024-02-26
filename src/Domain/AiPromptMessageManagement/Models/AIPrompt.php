<?php

namespace Domain\AiPromptMessageManagement\Models;

use Domain\AiPromptMessageManagement\Api\AiModelClientInterface;
use Domain\AiPromptMessageManagement\Api\GPT35AiModel;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAI\Laravel\Facades\OpenAI;
use Support\ValueObjects\PromptMessage;

/**
 * @property AiModelEnum $model
 * @property string $system
 * @property string $content
 * @property PromptMessage $system_prompt
 * @property PromptMessage $content_prompt
 * @property PromptMessageStatus $status
 * @property int $weight
 */
class AIPrompt extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'ai_prompts';

    protected $fillable = [
        'model',
        'status',
        'system',
        'content',
        'weight',
    ];

    protected $casts = [
        'weight' => 'int',
        'model' => AiModelEnum::class,
        'status' => PromptMessageStatus::class,
    ];

    public function questionVariant(): BelongsTo
    {
        return $this->belongsTo(QuestionVariant::class, 'question_variant_id');
    }

    public function promptable(): MorphTo
    {
        return $this->morphTo('promptable');
    }

    public function systemPrompt(): Attribute
    {
        return Attribute::make(get: function () {

            $replacers = match ($this->model) {
                AiModelEnum::Gpt_3_5 => ['_RESPONSE_JSON_STRUCTURE_' => config('aimodel.models.gpt-3-5-turbo.system_prompt')]
            };

            return PromptMessage::make($this->system, $replacers);
        });
    }

    /**
     * @throws \Exception
     */
    public function contentPrompt(): Attribute
    {
        return Attribute::make(get: function () {
            $replacers = match ($this->model) {
                AiModelEnum::Gpt_3_5 => []
            };

            return PromptMessage::make($this->content, $replacers);
        });
    }

    public function prompt(string $question, string $answer): string
    {
        return match ($this->model) {
            AiModelEnum::Gpt_3_5 => OpenAI::chat()->create([
                'model' => $this->model->value,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->system_prompt->toString(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->content_prompt->replaceMany([
                            '_QUESTION_TEXT_' => $question,
                            '_INTERVIEWEE_ANSWER_' => $answer,
                        ])->toString(),
                    ],
                ],
            ])->choices[0]->message->content
        };
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
