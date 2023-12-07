<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;

class CreateQuestionVariantAction
{
    protected QuestionVariantDto $questionVariantDto;

    public function __construct(
    ) {
    }

    public function execute(QuestionVariantDto $questionVariantDto): QuestionVariant
    {
        $this->questionVariantDto = $questionVariantDto;

        $question_variant = new QuestionVariant();

        $data = $this->questionVariantDto->except('creator', 'owner', 'ai_prompts')->toArray();

        $question_variant->fill($data)->save();

        $question_variant = $question_variant->refresh();

        $question_variant
            ->aiPrompts()
            ->createMany($questionVariantDto->ai_prompts);

        return $question_variant->load([
            'question',
            'creator',
        ]);
    }
}
