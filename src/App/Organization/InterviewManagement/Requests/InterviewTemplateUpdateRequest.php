<?php

namespace App\Organization\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterviewTemplateUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:3', 'max:255', Rule::unique(InterviewTemplate::class)
                ->where('organization_id',auth()->user()->organization_id)
                ->withoutTrashed()
                ->ignore($this->interviewTemplate())
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'availability_status' => ['filled', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'reusable' => ['sometimes', 'boolean'],
            'question_variant_ids' => ['filled', 'array', 'min:1'],
            'question_variant_ids.*' => ['required_with:question_variant_ids', 'numeric', Rule::exists('question_variants', 'id')->withoutTrashed(), 'distinct'],
        ];
    }

    public function interviewTemplate(): InterviewTemplate
    {
        return InterviewTemplate::query()->findOrFail($this->interview_template);
    }
}
