<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\Models\QuestionVariant;

class DeleteQuestionVariantAction
{
    public function __construct(public readonly int $questionVariant)
    {
    }

    public function execute():QuestionVariant
    {
        $question_variant = QuestionVariant::query()->findOrFail($this->questionVariant);

        $question_variant->delete();

        return $question_variant;
    }
}
