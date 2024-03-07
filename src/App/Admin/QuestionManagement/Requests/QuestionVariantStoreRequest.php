<?php

namespace App\Admin\QuestionManagement\Requests;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Domain\Organization\Models\Organization;
use Domain\AiPromptMessageManagement\Enums\AiModelEnum;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;

class QuestionVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'status' => ['required', new Enum(QuestionVariantStatusEnum::class)],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
            'organization_id' => ['required', Rule::exists(Organization::class, 'id')->withoutTrashed()],
            'ai_prompts' => ['required', 'array', 'min:1'],
            'ai_prompts.*' => ['required_with:ai_prompts', 'array'],
            'ai_prompts.*.model' => ['required', 'distinct', Rule::enum(AiModelEnum::class)],
            'ai_prompts.*.weight' => ['required', 'integer'],
            'ai_prompts.*.status' => ['required', new Enum(PromptMessageStatus::class)],
            'ai_prompts.*.content' => ['required', 'string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_QUESTION_TEXT_', '_INTERVIEWEE_ANSWER_'])) {
                        $fail('string should contains all these terms: '.Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
            'ai_prompts.*.system' => ['string',
                function ($attribute, $value, \Closure $fail) {
                    if ($this->aiModelsPlaceholdersMissing($value, $placeholders = ['_RESPONSE_JSON_STRUCTURE_'])) {
                        $fail('string should contain'.Arr::join($placeholders, ', ', 'and '));
                    }
                },
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->totalWeightNotEqual100()) {
                    $validator->errors()->add('weight', 'total selected models weights must equal 100.');
                }
            },
        ];
    }

    protected function totalWeightNotEqual100(): bool
    {
        return $this->collect('ai_prompts')
            ->where('status', PromptMessageStatus::Enabled->value)
            ->sum('weight') != 100;
    }

    private function aiModelsPlaceholdersMissing(string $prompt, array $placeholders): bool
    {
        return ! Str::containsAll($prompt, $placeholders);
    }
}
