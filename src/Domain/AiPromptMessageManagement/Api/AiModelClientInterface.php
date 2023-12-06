<?php

namespace Domain\AiPromptMessageManagement\Api;

use Domain\AiPromptMessageManagement\Models\AIPrompt;
use Domain\QuestionManagement\Models\QuestionVariant;

abstract class AiModelClientInterface
{
    public function __construct(
        public readonly AIPrompt $promptMessage
    ) {

    }

    abstract public function prompt(string $answerText): ?string;

    protected function question(): QuestionVariant
    {
        return $this->promptMessage->questionVariant;
    }
}
