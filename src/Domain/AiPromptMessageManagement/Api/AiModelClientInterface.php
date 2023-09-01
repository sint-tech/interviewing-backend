<?php

namespace Domain\AiPromptMessageManagement\Api;

use Domain\AiPromptMessageManagement\Models\AiPromptMessage;
use Domain\QuestionManagement\Models\QuestionVariant;

abstract class AiModelClientInterface
{
    public function __construct(
        public readonly AiPromptMessage $promptMessage
    ) {

    }

    abstract public function ask(string $answerText);

    protected function getQuestionVariantInstance(): QuestionVariant
    {
        return $this->promptMessage->questionVariant;
    }
}
