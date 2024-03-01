<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Domain\AiPromptMessageManagement\DataTransferObjects\PromptTemplateDto;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;

class UpdatePromptTemplateAction
{
    public function __construct(
        public PromptTemplate $promptTemplate,
        public readonly PromptTemplateDto $promptTemplateDto,
    ) {
    }

    public function execute(): PromptTemplate
    {
        if ($this->promptTemplateDto->is_selected) {
            PromptTemplate::query()
                ->where('name', $this->promptTemplateDto->name)
                ->whereKeyNot($this->promptTemplate->id)
                ->get()->each
                ->update(['is_selected' => false]);
        }

        $this->promptTemplate->update($this->promptTemplateDto->toArray());

        return $this->promptTemplate->refresh();
    }
}
