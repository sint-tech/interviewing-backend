<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;

class UpdateQuestionVariantAction
{
    public function __construct(
    ) {
    }

    public function execute(QuestionVariant $questionVariant, QuestionVariantDto $questionVariantDto): QuestionVariant
    {
        if ($questionVariant->question_id != $questionVariantDto->question_id) {
            $questionVariant->aiPrompts()->delete();
            $questionVariant->aiPrompts()->createMany($questionVariantDto->ai_prompts);
        }

        $questionVariant->update($questionVariantDto->except('ai_prompts')->toArray());

        return $questionVariant->refresh();
    }
}
