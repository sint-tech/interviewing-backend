<?php

namespace App\Admin\AIModelManagement\Requests;

use Support\Rules\TextContainsRule;
use Illuminate\Foundation\Http\FormRequest;
use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;

class PromptTemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'text' => ['required', 'string', new TextContainsRule(PromptTemplateVariableEnum::textVariables())],
            'stats_text' => ['required', 'string', new TextContainsRule(PromptTemplateVariableEnum::statsVariables())],
            'conclusion_text' => ['required', 'string'],
            'is_selected' => ['required', 'boolean'],
        ];
    }
}
