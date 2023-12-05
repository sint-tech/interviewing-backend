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
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAI\Laravel\Facades\OpenAI;
use Support\ValueObjects\PromptMessage;

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

    protected $table = 'ai_prompt_messages';

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

    public function systemPromptMessage(): Attribute
    {
        return Attribute::make(get: function () {

            $replacers = match ($this->aiModel->name) {
                AiModelEnum::Gpt_3_5 => ['_RESPONSE_JSON_STRUCTURE_' => json_encode([
                    'is_logical' => '<true,false>',
                    'rate' => '1 to 10',
                    'is_correct' => '<true,false>',
                    'answer_analysis' => "analysis interviewee's about the answer",
                ])]
            };

            return (string) PromptMessage::make($this->original['system_prompt'], $replacers);
        });
    }

    /**
     * @throws \Exception
     */
    public function contentPromptMessage(string $answerText): string
    {
        $replacers = match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => [
                '_QUESTION_TEXT_' => $this->questionVariant->text,
                '_INTERVIEWEE_ANSWER_' => $answerText,
            ]
        };

        return (string) PromptMessage::make($this->system_prompt, $replacers);
    }

    public function prompt(string $answer): string
    {
        return match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => OpenAI::chat()->create([
                'model' => $this->aiModel->name->value,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->system_prompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->contentPrompt($answer),
                    ],
                ],
            ])[0]->message->content
        };
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
