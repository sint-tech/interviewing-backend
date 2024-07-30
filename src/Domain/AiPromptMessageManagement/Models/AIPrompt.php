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
use Domain\AiPromptMessageManagement\Traits\ValidateJsonTrait;
use Domain\AnswerManagement\Enums\AnswerStatusEnum;
use Illuminate\Support\Facades\Config;

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
    use HasFactory, SoftDeletes, ValidateJsonTrait;

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
                AiModelEnum::Gpt_3_5 => ['_RESPONSE_JSON_STRUCTURE_' => Config::get('aimodel.models.gpt-3-5-turbo.system_prompt')]
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

    public function prompt(string $question, string $answer, string $job_title): array
    {
        $request = $this->createRequest($job_title, $question, $answer);
        $tries = Config::get('aimodel.tries', 3);
        $max_tries = $tries;

        while ($tries > 0) {
            $response = match ($this->model) {
                AiModelEnum::Gpt_3_5 => OpenAI::chat()->create($request),
                default => null
            };

            if ($this->isValidResponse($response->choices[0]->message->content)) {
                return $this->formatSuccessResponse($response, $request, $max_tries - $tries + 1);
            }

            $tries--;
        }

        return $this->formatFailureResponse($response, $request, $max_tries);
    }

    private function isValidResponse($response)
    {
        return $this->validateJson($response, [
            'english_score', 'correctness_rate', 'is_logical', 'is_correct',
            'answer_analysis', 'english_score_analysis'
        ]);
    }

    private function formatSuccessResponse($response, $request, $attempts)
    {
        return [
            'raw_prompt_request' => $request,
            'raw_prompt_response' => $response->choices[0]->message->content,
            'tries' => $attempts,
            'prompt' => json_decode($this->cleanString($response->choices[0]->message->content), true),
            'status' => AnswerStatusEnum::Successful->value,
        ];
    }

    private function formatFailureResponse($response, $request, $max_tries)
    {
        return [
            'ml_text_semantics' => $response,
            'raw_prompt_request' => $request,
            'raw_prompt_response' => $response->choices[0]->message->content,
            'tries' => $max_tries,
            'status' => AnswerStatusEnum::Failed->value,
            'prompt' => [
                'english_score' => 0,
                'correctness_rate' => 0,
                'is_logical' => false,
                'is_correct' => false,
                'answer_analysis' => 'No analysis available.',
                'english_score_analysis' => 'No analysis available.',
            ],
        ];
    }

    private function createRequest($job_title, $question, $answer)
    {
        return [
            'model' => $this->model->value,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->system_prompt->replaceMany([
                        '_JOB_TITLE_' => $job_title,
                    ])->toString(),
                ],
                [
                    'role' => 'user',
                    'content' => $this->content_prompt->replaceMany([
                        '_QUESTION_TEXT_' => $question,
                        '_INTERVIEWEE_ANSWER_' => $answer,
                    ])->toString(),
                ],
            ],
        ];
    }

    public function aiModelClientFactory(): AiModelClientInterface
    {
        return match ($this->aiModel->name) {
            AiModelEnum::Gpt_3_5 => new GPT35AiModel($this)
        };
    }
}
