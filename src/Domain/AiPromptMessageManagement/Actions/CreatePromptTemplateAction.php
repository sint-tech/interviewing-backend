<?php

namespace Domain\AiPromptMessageManagement\Actions;

use Exception;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\AiPromptMessageManagement\DataTransferObjects\PromptTemplateDto;


class CreatePromptTemplateAction
{
    /**
     * @throws Exception
     */
    public function execute(PromptTemplateDto $promptTemplateDto): PromptTemplate
    {
        if ($promptTemplateDto->is_selected) {
            PromptTemplate::where('name', $promptTemplateDto->name)->update(['is_selected' => false]);
        }

        $version = PromptTemplate::where('name', $promptTemplateDto->name)->max('version');
        $version = $version ? $version + 1 : 1;

        return PromptTemplate::create($promptTemplateDto->toArray() + [
            'version' => $version,
        ]);
    }
}
