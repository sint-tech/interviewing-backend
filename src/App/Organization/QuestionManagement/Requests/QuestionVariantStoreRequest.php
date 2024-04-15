<?php

namespace App\Organization\QuestionManagement\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;

class QuestionVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000',   Rule::unique('question_variants', 'text')
                ->where('organization_id', auth()->user()->organization_id)],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'status' => ['filled', new Enum(QuestionVariantStatusEnum::class)],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->selectedQuestionHasNoDefaultAIPrompt()) {
                    $validator->errors()->add('question_id', 'invalid question_id');
                }
            },
        ];
    }

    public function question(): Question
    {
        return Question::query()->find($this->validated('question_id'));
    }

    private function selectedQuestionHasNoDefaultAIPrompt(): bool
    {
        return $this->question()->defaultAIPrompt()->doesntExist();
    }
}
