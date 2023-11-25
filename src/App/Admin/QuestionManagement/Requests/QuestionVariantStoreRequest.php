<?php

namespace App\Admin\QuestionManagement\Requests;

use App\Admin\QuestionManagement\Requests\Traits\QuestionVariantOwnerTrait;
use Domain\AiPromptMessageManagement\Enums\PromptMessageStatus;
use Domain\AiPromptMessageManagement\Models\AIModel;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class QuestionVariantStoreRequest extends FormRequest
{
    use QuestionVariantOwnerTrait;

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
            'organization_id' => ['required', Rule::exists(Organization::class, 'id')->withoutTrashed()],
            'ai_models' => ['required', 'array', 'min:1'],
            'ai_models.*' => ['required_with:ai_models', 'array'],
            'ai_models.*.id' => ['required', 'distinct', Rule::exists(AIModel::class, 'id')],
            'ai_models.*.weight' => ['required', 'integer'],
            'ai_models.*.status' => ['required', new Enum(PromptMessageStatus::class)],
            'ai_models.*.content_prompt' => ['required', 'string',
                function ($attribute, $value, \Closure $fail) {
                    if (! Str::containsAll((string) $value, $replacers = ['_QUESTION_TEXT_', '_INTERVIEWEE_ANSWER_'])) {
                        $fail('string should contains all these terms: '.Arr::join($replacers, ', ', 'and '));
                    }
                },
            ],
            'ai_models.*.system_prompt' => ['string',
                function ($attribute, $value, \Closure $fail) {
                    if (! str_contains((string) $value, '_RESPONSE_JSON_STRUCTURE_')) {
                        $fail('string should contain _RESPONSE_JSON_STRUCTURE_');
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
        return $this->collect('ai_models')
            ->where('status', PromptMessageStatus::Enabled->value)
            ->sum('weight') != 100;
    }
}
