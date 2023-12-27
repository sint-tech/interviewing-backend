<?php

namespace App\Organization\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InterviewTemplateUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['filled', 'string', 'min:3', 'max:255', Rule::unique(InterviewTemplate::class)
                ->where('organization_id', auth()->user()->organization_id)
                ->withoutTrashed()
                ->ignore($this->interviewTemplate()),
            ],
            'job_profile_id' => ['nullable', Rule::exists(JobTitle::class, 'id')],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'availability_status' => ['filled', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'reusable' => ['sometimes', 'boolean'],
            'question_variant_ids' => ['filled', 'array', 'min:1'],
            'question_variant_ids.*' => ['required_with:question_variant_ids', 'numeric', Rule::exists('question_variants', 'id')->withoutTrashed(), 'distinct'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->interviewTemplate()->interviews()->exists()) {
                    $validator->errors()->add('interview_template', "Can't update this interview template as there are interviews use this template");
                }
            },
        ];
    }

    public function interviewTemplate(): InterviewTemplate
    {
        return $this->route('interview_template');
    }
}
