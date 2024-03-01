<?php
namespace Domain\AiPromptMessageManagement\Actions;

use App\Exceptions\CannotDeleteSelectedTemplateException;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;

class DeletePromptTemplateAction
{
    public function execute(PromptTemplate $promptTemplate): PromptTemplate
    {
        if ($promptTemplate->is_selected) {
            throw new CannotDeleteSelectedTemplateException();
        }

        $promptTemplate->delete();

        return $promptTemplate;
    }
}
