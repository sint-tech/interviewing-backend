<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Exception;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\AiPromptMessageManagement\DataTransferObjects\PromptTemplateDto;


class UpdatePromptTemplateAction
{
    public function __construct(
        public PromptTemplate $promptTemplate,
        public readonly PromptTemplateDto $promptTemplateDto,
    ) {
    }
    public function execute(): PromptTemplate
    {
        if($this->promptTemplateDto->is_selected) {
            PromptTemplate::where('name', $this->promptTemplateDto->name)->update(['is_selected' => false]);
        }

        $this->promptTemplate->update($this->promptTemplateDto->toArray());

        return $this->promptTemplate->refresh();
    }
}
