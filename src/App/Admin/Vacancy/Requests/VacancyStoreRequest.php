<?php

namespace App\Admin\Vacancy\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:250'],
            'description' => ['nullable', 'string', 'min:1', 'max:2000'],
            'started_at' => ['nullable', 'date_format:Y-m-d H:m', 'after:now'],
            'ended_at' => ['nullable', 'date_format:Y-m-d H:m', 'after:started_at'],
            'max_reconnection_tries' => ['required', 'min:0', 'max:5'],
            'organization_id' => ['nullable', Rule::exists(table_name(Organization::class), 'id')->withoutTrashed()],
            'open_positions' => ['required', 'integer', 'min:1'],
            'interview_template_id' => ['required', 'int', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
        ];
    }
}
