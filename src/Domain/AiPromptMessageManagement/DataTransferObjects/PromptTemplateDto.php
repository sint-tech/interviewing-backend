<?php

namespace Domain\AiPromptMessageManagement\DataTransferObjects;

use Spatie\LaravelData\Data;

class PromptTemplateDto extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $text,
        public readonly string $stats_text,
        public readonly string $conclusion_text,
        public readonly bool $is_selected,
    ) {

    }
}
