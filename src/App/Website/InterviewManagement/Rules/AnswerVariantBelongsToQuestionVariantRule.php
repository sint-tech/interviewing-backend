<?php

namespace App\Website\InterviewManagement\Rules;

use Domain\AnswerManagement\Models\AnswerVariant;
use Illuminate\Contracts\Validation\ValidationRule;

class AnswerVariantBelongsToQuestionVariantRule implements ValidationRule
{
    public function __construct(public int $questionVariantId)
    {
        //
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if ($this->answerNotBelongToTheQuestionVariant($value)) {
            $fail('The :attribute not belongs to this question variant.');
        }
    }


    protected function answerNotBelongToTheQuestionVariant(int $answer_variant_id): bool
    {
        return (bool) ! AnswerVariant::query()
            ->find($answer_variant_id)
            ?->questionVariant()
            ->whereKey($this->questionVariantId)
            ->exists();
    }
}
