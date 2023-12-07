<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class QuestionStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:1000'],
            'question_cluster_id' => ['required', Rule::exists('question_clusters', 'id')->withoutTrashed()],
            'difficult_level' => ['required', 'integer', 'min:1', 'max:10'],
            'question_type' => ['required', (new Enum(QuestionTypeEnum::class))],
            'min_reading_duration_in_seconds' => ['required', 'integer', 'min:1', 'lt:max_reading_duration_in_seconds'],
            'max_reading_duration_in_seconds' => ['required', 'integer', 'min:1', 'gt:min_reading_duration_in_seconds'],
            'ai_prompt' => ['required', 'array', 'min:1'],
            'ai_prompt.model' => ['required', 'distinct', Rule::enum(AiModelEnum::class)],
            'ai_prompt.content' => ['required', 'string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_QUESTION_TEXT_', '_INTERVIEWEE_ANSWER_'])) {
                        $fail('string should contains all these terms: '.Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
            'ai_prompt.system' => ['string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_RESPONSE_JSON_STRUCTURE_'])) {
                        $fail('string should contain'.Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
        ];
    }

    private function aiModelsPlaceholdersMissing(string $prompt, array $placeholders): bool
    {
        return ! Str::containsAll($prompt, $placeholders);
    }
}
