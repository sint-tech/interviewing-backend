<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AiPromptMessage;

class PromptAnswerAnalyticsAction
{
    public function __construct(
        public readonly AiPromptMessage $aiPromptMessage,
        public readonly string $answerText
    ) {
    }

    public function execute()
    {
        return $this->aiPromptMessage
            ->aiModelClientFactory()
            ->prompt($this->answerText);
    }
}
