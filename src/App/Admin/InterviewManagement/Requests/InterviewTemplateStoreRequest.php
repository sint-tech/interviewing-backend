<?php

namespace App\Admin\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class InterviewTemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255',
                Rule::unique(table_name(InterviewTemplate::class))
                    ->where('organization_id', $this->input('organization_id'))
                    ->withoutTrashed(),
            ],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'availability_status' => ['required', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'organization_id' => ['nullable', Rule::exists(table_name(Organization::class), 'id')->withoutTrashed()],
            'reusable' => ['sometimes', 'boolean'],
            'question_variant_ids' => ['required', 'array'],
            'question_variant_ids.*' => ['required', 'numeric', Rule::exists('question_variants', 'id')->withoutTrashed(), 'distinct'],
            'parent_id' => ['filled', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
        ];
    }

    public function questionVariants(): Collection
    {
        return QuestionVariant::query()
            ->whereKey($this->validated('question_variant_ids'))
            ->with('questionCluster')
            ->get();
    }
}
