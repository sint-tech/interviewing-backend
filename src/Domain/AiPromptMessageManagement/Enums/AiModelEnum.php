<?php

namespace Domain\AiPromptMessageManagement\Enums;

use Domain\AiPromptMessageManagement\Api\GPT35AiModel;
use Domain\AiPromptMessageManagement\Models\AiPromptMessage;

enum AiModelEnum: string
{
    case Gpt_3_5 = 'gpt-3.5-turbo';

    /**
     * @param AiPromptMessage $promptMessage
     * @param string $answer
     * @return string|null
     */
    public function prompt(AiPromptMessage $promptMessage,string $answer): string| null
    {
        return match ($this) {
            self::Gpt_3_5 => (new GPT35AiModel($promptMessage))->prompt($answer)
        };
    }
}
