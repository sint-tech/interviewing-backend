<?php

namespace App\Organization\InterviewManagement\Requests;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\QuestionManagement\Models\QuestionVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterviewTemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:3', 'max:1000'],
            'reusable' => ['required', 'boolean'],
            'availability_status' => ['required', Rule::enum(InterviewTemplateAvailabilityStatusEnum::class)],
            'question_variants' => ['required', 'array', 'min:1', Rule::exists(table_name(QuestionVariant::class), 'id')->withoutTrashed()],
        ];
    }
}
