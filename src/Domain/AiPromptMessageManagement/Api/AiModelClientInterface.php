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

    abstract public function prompt(string $answerText): string|null;

    protected function question(): QuestionVariant
    {
        return $this->promptMessage->questionVariant;
    }
}
