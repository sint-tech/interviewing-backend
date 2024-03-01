<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Domain\AiPromptMessageManagement\DataTransferObjects\PromptTemplateDto;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Exception;

class CreatePromptTemplateAction
{
    /**
     * @throws Exception
     */
    public function execute(PromptTemplateDto $promptTemplateDto): PromptTemplate
    {
        if ($promptTemplateDto->is_selected) {
            PromptTemplate::query()->where('name', $promptTemplateDto->name)->get()->each->update(['is_selected' => false]);
        }

        return PromptTemplate::query()->create($promptTemplateDto->toArray() + [
            'version' => $this->nextVersion($promptTemplateDto->name),
        ]);
    }

    protected function nextVersion(string $templateName): int
    {
        return ((int) PromptTemplate::query()->where('name', $templateName)->max('version')) + 1;
    }
}
