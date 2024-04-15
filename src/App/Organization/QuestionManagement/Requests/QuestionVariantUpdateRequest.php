<?php

namespace App\Organization\QuestionManagement\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;

class QuestionVariantUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['filled', 'string', 'min:3', 'max:1000', Rule::unique('question_variants', 'text')
                ->where('organization_id', $this->questionVariant()->organization_id)
                ->ignore($this->questionVariant()->id)],
            'description' => ['filled', 'string', 'min:3', 'max:1000'],
            'question_id' => ['filled', Rule::exists('questions', 'id')->withoutTrashed()],
            'status' => ['filled', new Enum(QuestionVariantStatusEnum::class)],
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
                if ($this->questionVariant()->status == QuestionVariantStatusEnum::Public->value && $this->questionVariant()->inInterviewTemplates()) {
                    $validator->errors()->add('status', 'Can\'t update this question variant as there are interview templates use this question variant');
                }
                if ($this->questionVariant()->status == QuestionVariantStatusEnum::Private->value && $this->questionVariant()->inRunningVacancies()) {
                    $validator->errors()->add('status', 'Can\'t update this question variant as there are interview templates use this question variant');
                }
            },
        ];
    }

    public function questionVariant(): QuestionVariant
    {
        return $this->route('question_variant');
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
