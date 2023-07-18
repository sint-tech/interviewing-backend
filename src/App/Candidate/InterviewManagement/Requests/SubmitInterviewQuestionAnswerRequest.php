<?php

namespace App\Candidate\InterviewManagement\Requests;

use App\Candidate\InterviewManagement\Rules\AnswerVariantBelongsToQuestionVariantRule;
use Domain\InterviewManagement\Enums\QuestionOccurrenceReasonEnum;
use Domain\InterviewManagement\Models\Answer;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitInterviewQuestionAnswerRequest extends FormRequest
{
    public function authorize()
    {
        return $this->interview->candidate_id == auth()->id();
    }

    public function rules(): array
    {
        return [
            'question_occurrence_reason' => ['required', 'string',
                Rule::enum(QuestionOccurrenceReasonEnum::class),
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($this->questionAnsweredBefore()) {
                        $fail('This question variant is answered Before');
                    }
                },
            ],
            'question_variant_id' => ['required',
                Rule::exists('interview_template_questions', 'question_variant_id')
                    ->where('interview_template_id', $this->interview->interview_template_id)
                    ->withoutTrashed(),
            ],
            'answer_text' => ['required', 'string','min:3','max:1000'],
        ];
    }

    protected function questionAnsweredBefore(): bool
    {
        return Answer::query()
            ->where('interview_id', $this->interview->getKey())
            ->where('answer_variant_id', $this->input('answer_variant_id'))
            ->where('question_variant_id', $this->input('question_variant_id'))
            ->exists();
    }

    public function questionVariant():QuestionVariant
    {
        return QuestionVariant::query()->find($this->validated('question_variant_id'));
    }
}
