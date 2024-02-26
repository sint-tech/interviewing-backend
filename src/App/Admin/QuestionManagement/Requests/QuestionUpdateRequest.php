<?php

namespace App\Admin\QuestionManagement\Requests;

use Domain\QuestionManagement\Enums\QuestionTypeEnum;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class QuestionUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:1000'],
            'question_cluster_id' => ['filled', Rule::exists('question_clusters', 'id')->withoutTrashed()],
            'difficult_level' => ['filled', 'integer', 'min:1', 'max:10'],
            'question_type' => ['filled', (new Enum(QuestionTypeEnum::class))],
            'min_reading_duration_in_seconds' => ['filled', 'integer', 'min:1', 'lt:max_reading_duration_in_seconds'],
            'max_reading_duration_in_seconds' => ['filled', 'integer', 'min:1', 'gt:min_reading_duration_in_seconds'],
            'ai_prompt' => ['filled', 'array', 'min:1'],
            'ai_prompt.model' => ['filled', 'distinct', Rule::enum(AiModelEnum::class)],
            'ai_prompt.content' => [
                'filled', 'string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_QUESTION_TEXT_', '_INTERVIEWEE_ANSWER_'])) {
                        $fail('string should contains all these terms: ' . Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
            'ai_prompt.system' => [
                'string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_RESPONSE_JSON_STRUCTURE_'])) {
                        $fail('string should contain' . Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
        ];
    }

    public function question(): Question
    {
        return $this->route('question');
    }

    private function aiModelsPlaceholdersMissing(string $value, array $placeholders): bool
    {
        return !Str::contains($value, $placeholders);
    }
}
