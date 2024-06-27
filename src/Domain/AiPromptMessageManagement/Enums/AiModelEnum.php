<?php

namespace Domain\AiPromptMessageManagement\Enums;

enum AiModelEnum: int
{
    case Gpt_3_5 = 1;
    case Gpt_4o = 2;

    public static function getValues(): array
    {
        return [
            self::Gpt_3_5->value,
            self::Gpt_4o->value,
        ];
    }

    public function getModelName(): string
    {
        return match ($this) {
            self::Gpt_3_5 => 'gpt-3.5-turbo',
            self::Gpt_4o => 'gpt-4o',
        };
    }
}
