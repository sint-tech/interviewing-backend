<?php

namespace Domain\QuestionManagement\Actions;

use Domain\QuestionManagement\DataTransferObjects\QuestionVariantDto;
use Domain\QuestionManagement\Models\QuestionVariant;

class CreateQuestionVariantAction
{
    public function __construct(
        public QuestionVariantDto $questionVariantDto
    ) {
    }

    public function execute(): QuestionVariant
    {
        $question_variant = new QuestionVariant();

        $data = $this->questionVariantDto->toArray();

        $data = array_merge($data, [
            'creator_type' => $this->questionVariantDto->creator::class,
            'creator_id' => $this->questionVariantDto->creator->getKey(),
            'owner_type' => $this->questionVariantDto->owner::class,
            'owner_id' => $this->questionVariantDto->owner->getKey(),
        ]);

        $question_variant->fill($data)->save();

        return $question_variant->refresh()->load([
            'question',
            'owner',
            'creator',
        ]);
    }
}
