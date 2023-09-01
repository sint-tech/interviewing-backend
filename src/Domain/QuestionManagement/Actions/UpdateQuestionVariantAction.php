<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionData;
use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;

class UpdateQuestionVariantAction
{

    public function __construct(
        public QuestionVariant $questionVariant,
        public readonly QuestionVariantDto $questionVariantDto
    )
    {}

    public function execute():QuestionVariant
    {
        $this->questionVariant->update($this->questionVariantDto->except('creator','owner')->toArray());

        return $this->questionVariant->refresh();
    }
}
