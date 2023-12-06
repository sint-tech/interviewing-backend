<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Domain\AiPromptMessageManagement\Models\AIPrompt;

class PromptAnswerAnalyticsAction
{
    public function __construct(
        public readonly AIPrompt $aiPromptMessage,
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
