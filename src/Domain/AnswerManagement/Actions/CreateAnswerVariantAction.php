<?php

namespace Domain\AnswerManagement\Actions;

use Domain\AnswerManagement\DataTransferObjects\AnswerVariantDto;
use Domain\AnswerManagement\Models\AnswerVariant;

class CreateAnswerVariantAction
{
    public function __construct(public readonly AnswerVariantDto $answerVariantDto)
    {

    }

    public function execute(): AnswerVariant
    {
        $answer_variant = new AnswerVariant($this->answerVariantDto->toArray());

        $answer_variant->save();

        return $answer_variant->load('answer');
    }
}
