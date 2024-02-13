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
        $questionVariant->update($questionVariantDto->except('creator')->toArray());

        return $questionVariant->refresh();
    }
}
