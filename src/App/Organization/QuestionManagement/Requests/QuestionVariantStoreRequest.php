<?php

namespace App\Organization\QuestionManagement\Requests;

use Domain\QuestionManagement\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionVariantStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:3', 'max:1000'],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'question_id' => ['required', Rule::exists('questions', 'id')->withoutTrashed()],
            'reading_time_in_seconds' => ['required', 'integer', 'min:1'],
            'answering_time_in_seconds' => ['required', 'integer', 'min:1'],
        ];
    }

    public function question(): Question
    {
        return Question::query()->find($this->validated('question_id'));
    }
}
