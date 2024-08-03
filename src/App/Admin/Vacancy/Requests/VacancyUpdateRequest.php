<?php

namespace App\Admin\Vacancy\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['filled', 'string', 'min:3', 'max:250'],
            'description' => ['nullable', 'string', 'min:1', 'max:2000'],
            'started_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:now'],
            'ended_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:started_at'],
            'max_reconnection_tries' => ['filled', 'min:0', 'max:5'],
            'organization_id' => ['nullable', Rule::exists(table_name(Organization::class), 'id')->withoutTrashed()],
            'open_positions' => ['filled', 'integer', 'min:1'],
            'interview_template_id' => ['filled', 'int', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
            'is_public' => ['filled', 'boolean'],
        ];
    }
}
