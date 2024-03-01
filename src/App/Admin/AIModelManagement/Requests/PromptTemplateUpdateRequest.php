<?php

namespace App\Admin\AIModelManagement\Requests;

use Support\Rules\TextContainsRule;
use Illuminate\Foundation\Http\FormRequest;
use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;

class PromptTemplateUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'max:50', 'in:impacts,candidate_advices,recruiter_advices'],
            'text' => ['filled', 'string', new TextContainsRule(PromptTemplateVariableEnum::textVariables())],
            'stats_text' => ['filled', 'string', new TextContainsRule(PromptTemplateVariableEnum::statsVariables())],
            'conclusion_text' => ['filled', 'string'],
            'is_selected' => ['filled', 'boolean'],
        ];
    }
}
