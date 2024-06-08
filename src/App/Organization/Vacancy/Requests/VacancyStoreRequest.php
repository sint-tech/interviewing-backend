<?php

namespace App\Organization\Vacancy\Requests;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3', 'max:250'],
            'description' => ['nullable', 'min:3', 'max:1000'],
            'max_reconnection_tries' => ['required', 'min:0', 'max:1000'],
            'open_positions' => ['required', 'min:1', 'max:1000'],
            'started_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:now'],
            'ended_at' => ['nullable', 'date_format:Y-m-d H:i', 'after:started_at'],
            'interview_template_id' => ['required', 'int', Rule::exists(table_name(InterviewTemplate::class), 'id')->withoutTrashed()],
            //todo add current trial for interview
        ];
    }

    public function messages(): array
    {
        return [
            'started_at.after' => "The 'Start Time' field must be a date and time after the current time.",
            'ended_at.after' => "The 'End Time' field must be a date and time after the 'Start Time' field.",
        ];
    }
}
