<?php

namespace App\Admin\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterviewTemplateUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:3', 'max:255', Rule::unique(InterviewTemplate::class)
                ->where('organization_id', $this->interviewTemplate()->organization_id)
                ->withoutTrashed()
                ->ignore($this->interviewTemplate()),
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'availability_status' => ['filled', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'job_profile_id' => ['filled', Rule::exists(JobTitle::class, 'id')->withoutTrashed()],
            'reusable' => ['sometimes', 'boolean'],
            'question_variant_ids' => ['filled', 'array', 'min:1'],
            'question_variant_ids.*' => ['required_with:question_variant_ids', 'numeric', Rule::exists('question_variants', 'id')->withoutTrashed(), 'distinct'],
        ];
    }

    public function interviewTemplate(): InterviewTemplate
    {
        return InterviewTemplate::query()->find($this->interview_template);
    }
}
