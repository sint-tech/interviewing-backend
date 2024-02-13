<?php

namespace App\Organization\QuestionManagement\Requests;

use Domain\QuestionManagement\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class QuestionVariantUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['filled', 'string', 'min:3', 'max:1000'],
            'description' => ['filled', 'string', 'min:3', 'max:1000'],
            'question_id' => ['filled', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['filled', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['filled', 'integer', 'min:1'],
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
